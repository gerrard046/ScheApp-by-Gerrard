<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Models\ZenSession;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    // ============================
    // TITLE SYSTEM
    // ============================
    private function getTitleForLevel($level) {
        $titles = [
            1 => 'Pemula',
            2 => 'Pejuang Baru',
            3 => 'Pengelola Waktu',
            5 => 'Penguasa Jadwal',
            7 => 'Ahli Produktivitas',
            10 => 'Grand Master',
            15 => 'Legenda Waktu',
            20 => 'Dewa Produktivitas',
            25 => 'Maha Guru Agung',
            30 => 'Ascended Master',
        ];
        
        $title = 'Pemula';
        foreach ($titles as $minLevel => $t) {
            if ($level >= $minLevel) $title = $t;
        }
        return $title;
    }

    // ============================
    // XP BONUS CALCULATOR
    // ============================
    private function calculateXpBonus($schedule, $user) {
        $baseXp = 10;
        $bonusDetails = [];

        // Priority Bonus
        $priorityBonus = ['high' => 15, 'med' => 5, 'low' => 2];
        $pBonus = $priorityBonus[$schedule->priority] ?? 0;
        if ($pBonus > 0) {
            $baseXp += $pBonus;
            $bonusDetails[] = "Prioritas +" . $pBonus . "XP";
        }

        // Early Completion Bonus (selesai sebelum tenggat)
        $deadline = Carbon::parse($schedule->date . ' ' . $schedule->time);
        if (Carbon::now()->lt($deadline)) {
            $hoursEarly = Carbon::now()->diffInHours($deadline);
            $earlyBonus = min($hoursEarly * 2, 30); // Max 30 XP bonus
            $baseXp += $earlyBonus;
            $bonusDetails[] = "Selesai Awal +" . $earlyBonus . "XP 🏃";
        }

        // Combo Bonus
        $comboBonus = min($user->combo_count * 3, 30); // Max 30 XP from combo
        if ($comboBonus > 0) {
            $baseXp += $comboBonus;
            $bonusDetails[] = "Combo x" . $user->combo_count . " +" . $comboBonus . "XP 🔥";
        }

        // Streak Bonus (extra for maintained streaks)
        if ($user->streak >= 7) {
            $streakBonus = min($user->streak, 20);
            $baseXp += $streakBonus;
            $bonusDetails[] = "Streak " . $user->streak . " hari +" . $streakBonus . "XP";
        }

        return [
            'total' => $baseXp,
            'details' => $bonusDetails,
        ];
    }

    // ============================
    // BADGE CHECKER
    // ============================
    private function checkAndAwardBadges($user) {
        $badges = $user->badges ?? [];
        $newBadges = [];
        $completedCount = Schedule::where('user_id', $user->id)->where('is_completed', true)->count();
        $earlyCount = $user->total_early_completions;

        $badgeDefinitions = [
            ['id' => 'first_task', 'name' => 'Langkah Pertama', 'icon' => '🌟', 'desc' => 'Selesaikan tugas pertama', 'condition' => $completedCount >= 1],
            ['id' => 'five_tasks', 'name' => 'Mulai Konsisten', 'icon' => '✨', 'desc' => 'Selesaikan 5 tugas', 'condition' => $completedCount >= 5],
            ['id' => 'ten_tasks', 'name' => 'Pekerja Keras', 'icon' => '🔥', 'desc' => 'Selesaikan 10 tugas', 'condition' => $completedCount >= 10],
            ['id' => 'twenty_five', 'name' => 'Quarter Master', 'icon' => '⚡', 'desc' => 'Selesaikan 25 tugas', 'condition' => $completedCount >= 25],
            ['id' => 'fifty_tasks', 'name' => 'Diamond Hustle', 'icon' => '💎', 'desc' => 'Selesaikan 50 tugas', 'condition' => $completedCount >= 50],
            ['id' => 'century', 'name' => 'Century Club', 'icon' => '👑', 'desc' => 'Selesaikan 100 tugas', 'condition' => $completedCount >= 100],
            ['id' => 'early_bird', 'name' => 'Early Bird', 'icon' => '🐦', 'desc' => 'Selesaikan 5 tugas sebelum tenggat', 'condition' => $earlyCount >= 5],
            ['id' => 'speed_demon', 'name' => 'Speed Demon', 'icon' => '⚡', 'desc' => 'Selesaikan 20 tugas sebelum tenggat', 'condition' => $earlyCount >= 20],
            ['id' => 'time_lord', 'name' => 'Time Lord', 'icon' => '⏳', 'desc' => 'Selesaikan 50 tugas sebelum tenggat', 'condition' => $earlyCount >= 50],
            ['id' => 'streak_3', 'name' => 'Tiga Hari Berturut', 'icon' => '🔥', 'desc' => 'Raih 3 hari streak', 'condition' => $user->streak >= 3],
            ['id' => 'streak_7', 'name' => 'Unstoppable', 'icon' => '💪', 'desc' => 'Raih 7 hari streak', 'condition' => $user->streak >= 7],
            ['id' => 'streak_30', 'name' => 'Iron Will', 'icon' => '🏅', 'desc' => 'Raih 30 hari streak', 'condition' => $user->streak >= 30],
            ['id' => 'combo_5', 'name' => 'Combo Master', 'icon' => '🎯', 'desc' => 'Raih combo 5x', 'condition' => $user->highest_combo >= 5],
            ['id' => 'combo_10', 'name' => 'Unstoppable Combo', 'icon' => '💥', 'desc' => 'Raih combo 10x', 'condition' => $user->highest_combo >= 10],
            ['id' => 'level_5', 'name' => 'Power User', 'icon' => '⚡', 'desc' => 'Capai Level 5', 'condition' => $user->level >= 5],
            ['id' => 'level_10', 'name' => 'Legend', 'icon' => '🏆', 'desc' => 'Capai Level 10', 'condition' => $user->level >= 10],
            ['id' => 'level_20', 'name' => 'Mythical', 'icon' => '🐉', 'desc' => 'Capai Level 20', 'condition' => $user->level >= 20],
            ['id' => 'zen_1h', 'name' => 'Zen Master', 'icon' => '🧘', 'desc' => 'Total 60 menit Zen', 'condition' => ($user->zenSessions()->sum('duration_minutes') ?? 0) >= 60],
            ['id' => 'zen_5h', 'name' => 'Deep Focus', 'icon' => '🧊', 'desc' => 'Total 300 menit Zen', 'condition' => ($user->zenSessions()->sum('duration_minutes') ?? 0) >= 300],
        ];

        foreach ($badgeDefinitions as $badge) {
            if ($badge['condition'] && !in_array($badge['id'], $badges)) {
                $badges[] = $badge['id'];
                $newBadges[] = $badge;
            }
        }

        if (count($newBadges) > 0) {
            $user->badges = $badges;
            $user->save();
        }

        return $newBadges;
    }

    public function index(Request $request) {
        $now = Carbon::now();
        $user = auth()->user()->load(['zenSessions']);
        
        $query = Schedule::with(['subTasks', 'group', 'parentTask']);
        
        // Filter by user OR group member
        if ($user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('group', function($g) use ($user) {
                      $g->whereHas('members', function($m) use ($user) {
                          $m->where('users.id', $user->id);
                      });
                  });
            });
        }

        $allSchedules = $query->get();
        
        // AI Briefing Logic
        $todayTasks = $allSchedules->where('date', date('Y-m-d'))->where('is_completed', false);
        $briefing = $this->generateAIBriefing($todayTasks, $user);

        // Heatmap Data (Advanced GitHub Style)
        $heatmap = $this->getAdvancedHeatmap($user);

        // Analytics: Deep Work Correlation
        $zenCorrelation = $this->getZenCorrelation($user);

        // Map data with status and score
        $dataWithStatus = $allSchedules->map(function ($item) use ($now) {
            $scheduleTime = Carbon::parse($item->date . ' ' . $item->time);
            $item->is_missed = $scheduleTime->isPast() && !$item->is_completed;
            
            // Check early completion
            if ($item->is_completed && $item->completed_at) {
                $completedAt = Carbon::parse($item->completed_at);
                $item->completed_early = $completedAt->lt($scheduleTime);
                $item->hours_early = $item->completed_early ? $completedAt->diffInHours($scheduleTime) : 0;
            } else {
                $item->completed_early = false;
                $item->hours_early = 0;
            }

            $score = 0;
            if (!$item->is_completed && !$item->is_missed) {
                $priorityScores = ['high' => 50, 'med' => 30, 'low' => 10];
                $score += $priorityScores[$item->priority] ?? 0;
                $hoursLeft = $now->diffInHours($scheduleTime, false);
                if ($hoursLeft >= 0 && $hoursLeft <= 24) $score += (24 - $hoursLeft) * 2;
            }
            $item->ai_score = $score;
            return $item;
        });

        $sortedSchedules = $dataWithStatus->sortByDesc(function ($item) {
            if ($item->is_completed) return -1000;
            if ($item->is_missed) return -500;
            return $item->ai_score;
        })->values();

        // Leaderboard mini (top 5 users)
        $leaderboard = User::orderByDesc('xp')
            ->take(5)
            ->get(['id', 'name', 'xp', 'level', 'title', 'avatar_color', 'streak']);

        $stats = [
            'total' => $allSchedules->count(),
            'today' => $todayTasks->count(),
            'completion_rate' => $allSchedules->count() > 0 ? round(($allSchedules->where('is_completed', true)->count() / $allSchedules->count()) * 100) : 0,
            'xp' => $user->xp,
            'level' => $user->level,
            'title' => $user->title ?? $this->getTitleForLevel($user->level),
            'streak' => $user->streak,
            'combo' => $user->combo_count ?? 0,
            'highest_combo' => $user->highest_combo ?? 0,
            'total_early' => $user->total_early_completions ?? 0,
            'xp_next' => $user->level * 100,
            'heatmap' => $heatmap,
            'zen_correlation' => $zenCorrelation,
            'briefing' => $briefing
        ];
        
        $groups = $user->administeredGroups->merge($user->groups)->unique('id');

        // Get user badges for display
        $userBadges = $this->getAllBadgesWithStatus($user);

        return view('schedules.index', [
            'schedules' => $sortedSchedules, 
            'stats' => $stats,
            'groups' => $groups,
            'leaderboard' => $leaderboard,
            'userBadges' => $userBadges,
        ]);
    }

    private function getAllBadgesWithStatus($user) {
        $earnedBadges = $user->badges ?? [];
        $completedCount = Schedule::where('user_id', $user->id)->where('is_completed', true)->count();
        $earlyCount = $user->total_early_completions ?? 0;
        $zenTotal = $user->zenSessions()->sum('duration_minutes') ?? 0;

        $all = [
            ['id' => 'first_task', 'name' => 'Langkah Pertama', 'icon' => '🌟', 'desc' => 'Selesaikan tugas pertama', 'unlocked' => in_array('first_task', $earnedBadges)],
            ['id' => 'five_tasks', 'name' => 'Mulai Konsisten', 'icon' => '✨', 'desc' => 'Selesaikan 5 tugas', 'unlocked' => in_array('five_tasks', $earnedBadges)],
            ['id' => 'ten_tasks', 'name' => 'Pekerja Keras', 'icon' => '🔥', 'desc' => 'Selesaikan 10 tugas', 'unlocked' => in_array('ten_tasks', $earnedBadges)],
            ['id' => 'twenty_five', 'name' => 'Quarter Master', 'icon' => '⚡', 'desc' => 'Selesaikan 25 tugas', 'unlocked' => in_array('twenty_five', $earnedBadges)],
            ['id' => 'fifty_tasks', 'name' => 'Diamond Hustle', 'icon' => '💎', 'desc' => 'Selesaikan 50 tugas', 'unlocked' => in_array('fifty_tasks', $earnedBadges)],
            ['id' => 'century', 'name' => 'Century Club', 'icon' => '👑', 'desc' => 'Selesaikan 100 tugas', 'unlocked' => in_array('century', $earnedBadges)],
            ['id' => 'early_bird', 'name' => 'Early Bird', 'icon' => '🐦', 'desc' => '5 tugas sebelum tenggat', 'unlocked' => in_array('early_bird', $earnedBadges)],
            ['id' => 'speed_demon', 'name' => 'Speed Demon', 'icon' => '⚡', 'desc' => '20 tugas sebelum tenggat', 'unlocked' => in_array('speed_demon', $earnedBadges)],
            ['id' => 'time_lord', 'name' => 'Time Lord', 'icon' => '⏳', 'desc' => '50 tugas sebelum tenggat', 'unlocked' => in_array('time_lord', $earnedBadges)],
            ['id' => 'streak_3', 'name' => '3 Hari Berturut', 'icon' => '🔥', 'desc' => 'Raih 3 hari streak', 'unlocked' => in_array('streak_3', $earnedBadges)],
            ['id' => 'streak_7', 'name' => 'Unstoppable', 'icon' => '💪', 'desc' => 'Raih 7 hari streak', 'unlocked' => in_array('streak_7', $earnedBadges)],
            ['id' => 'streak_30', 'name' => 'Iron Will', 'icon' => '🏅', 'desc' => 'Raih 30 hari streak', 'unlocked' => in_array('streak_30', $earnedBadges)],
            ['id' => 'combo_5', 'name' => 'Combo Master', 'icon' => '🎯', 'desc' => 'Raih combo 5x', 'unlocked' => in_array('combo_5', $earnedBadges)],
            ['id' => 'combo_10', 'name' => 'Unstoppable Combo', 'icon' => '💥', 'desc' => 'Raih combo 10x', 'unlocked' => in_array('combo_10', $earnedBadges)],
            ['id' => 'level_5', 'name' => 'Power User', 'icon' => '⚡', 'desc' => 'Capai Level 5', 'unlocked' => in_array('level_5', $earnedBadges)],
            ['id' => 'level_10', 'name' => 'Legend', 'icon' => '🏆', 'desc' => 'Capai Level 10', 'unlocked' => in_array('level_10', $earnedBadges)],
            ['id' => 'level_20', 'name' => 'Mythical', 'icon' => '🐉', 'desc' => 'Capai Level 20', 'unlocked' => in_array('level_20', $earnedBadges)],
            ['id' => 'zen_1h', 'name' => 'Zen Master', 'icon' => '🧘', 'desc' => 'Total 60 menit Zen', 'unlocked' => in_array('zen_1h', $earnedBadges)],
            ['id' => 'zen_5h', 'name' => 'Deep Focus', 'icon' => '🧊', 'desc' => 'Total 300 menit Zen', 'unlocked' => in_array('zen_5h', $earnedBadges)],
        ];

        return $all;
    }

    public function kanban()
    {
        $user = auth()->user();
        $query = Schedule::with(['group', 'parentTask', 'subTasks']);
        
        if ($user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('group', function($g) use ($user) {
                      $g->whereHas('members', function($m) use ($user) {
                          $m->where('users.id', $user->id);
                      });
                  });
            });
        }

        $tasks = $query->get();
        
        // Add early completion info
        $tasks->each(function($task) {
            if ($task->is_completed && $task->completed_at) {
                $deadline = Carbon::parse($task->date . ' ' . $task->time);
                $completedAt = Carbon::parse($task->completed_at);
                $task->completed_early = $completedAt->lt($deadline);
                $task->hours_early = $task->completed_early ? $completedAt->diffInHours($deadline) : 0;
            } else {
                $task->completed_early = false;
                $task->hours_early = 0;
            }
        });
        
        $board = [
            'todo' => $tasks->where('is_completed', false)->filter(fn($t) => Carbon::parse($t->date)->isFuture() || Carbon::parse($t->date)->isToday()),
            'missed' => $tasks->where('is_completed', false)->filter(fn($t) => Carbon::parse($t->date)->isPast() && !Carbon::parse($t->date)->isToday()),
            'done' => $tasks->where('is_completed', true),
        ];

        return view('schedules.kanban', compact('board'));
    }

    public function toggleComplete(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $user = auth()->user();
        
        // --- Dependency Check ---
        if ($schedule->dependency_id) {
            $parent = Schedule::find($schedule->dependency_id);
            if ($parent && !$parent->is_completed) {
                return redirect()->back()->with('error', "🚨 Tugas ini prasyarat dari '" . $parent->activity_name . "'. Selesaikan itu dulu!");
            }
        }

        if ($schedule->user_id === $user->id) {
            if ($request->hasFile('proof_image')) {
                $path = $request->file('proof_image')->store('proofs', 'public');
                $schedule->proof_image = $path;
            }

            $wasCompleted = $schedule->is_completed;
            $schedule->is_completed = !$schedule->is_completed;
            $schedule->is_verified = false;
            
            if ($schedule->is_completed) {
                $schedule->completed_at = Carbon::now();
            } else {
                $schedule->completed_at = null;
            }
            
            $schedule->save();

            if ($schedule->is_completed) {
                // Check if completed before deadline
                $deadline = Carbon::parse($schedule->date . ' ' . $schedule->time);
                $isEarly = Carbon::now()->lt($deadline);
                
                if ($isEarly) {
                    $user->total_early_completions = ($user->total_early_completions ?? 0) + 1;
                }

                // Combo system
                $user->combo_count = ($user->combo_count ?? 0) + 1;
                if ($user->combo_count > ($user->highest_combo ?? 0)) {
                    $user->highest_combo = $user->combo_count;
                }

                // Calculate XP with bonuses
                $xpResult = $this->calculateXpBonus($schedule, $user);
                $xpEarned = $xpResult['total'];
                
                $user->xp += $xpEarned;
                $user->total_xp_earned = ($user->total_xp_earned ?? 0) + $xpEarned;
                
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                
                if ($user->last_activity_date == $yesterday) $user->streak += 1;
                elseif ($user->last_activity_date != $today) $user->streak = 1;
                $user->last_activity_date = $today;

                // Check level up
                $leveledUp = false;
                while ($user->xp >= ($user->level * 100)) {
                    $user->xp -= ($user->level * 100);
                    $user->level += 1;
                    $leveledUp = true;
                }
                
                // Update title
                $user->title = $this->getTitleForLevel($user->level);
                $user->save();

                // Check for new badges
                $newBadges = $this->checkAndAwardBadges($user);
                
                // Build success message
                $msg = "Tugas selesai! +" . $xpEarned . "XP";
                if ($isEarly) $msg .= " 🏃 Selesai sebelum tenggat!";
                if (count($xpResult['details']) > 0) {
                    $msg .= " (" . implode(', ', $xpResult['details']) . ")";
                }
                
                if ($leveledUp) {
                    return redirect()->back()->with('levelup', '🎉 Level Up! LVL ' . $user->level . ' — ' . $user->title);
                }
                
                if (count($newBadges) > 0) {
                    $badgeNames = collect($newBadges)->map(fn($b) => $b['icon'] . ' ' . $b['name'])->implode(', ');
                    $msg .= " | Badge Baru: " . $badgeNames;
                }
                
                return redirect()->back()->with('success', $msg);
            } else {
                // Undo completion
                $user->xp = max(0, $user->xp - 10);
                $user->combo_count = 0; // Reset combo on undo
                $user->save();
            }
            return redirect()->back()->with('success', 'Status diperbarui.');
        }

        // Admin verification logic...
        if ($user->role === 'admin' && $schedule->is_completed) {
            $schedule->is_verified = !$schedule->is_verified;
            $schedule->save();
            if ($schedule->is_verified) {
                $taskOwner = $schedule->user;
                if ($taskOwner) { $taskOwner->xp += 5; $taskOwner->save(); }
                return redirect()->back()->with('success', 'Diverifikasi!');
            }
        }

        return redirect()->back()->with('error', 'Akses ditolak.');
    }

    public function logZenSession(Request $request)
    {
        $request->validate(['minutes' => 'required|integer']);
        ZenSession::create([
            'user_id' => auth()->id(),
            'duration_minutes' => $request->minutes,
            'date' => date('Y-m-d')
        ]);
        return response()->json(['success' => true]);
    }

    private function generateAIBriefing($tasks, $user)
    {
        $count = $tasks->count();
        if ($count == 0) return "Hari ini jadwalmu kosong. Waktunya eksplorasi atau istirahat total! 🧊";
        
        $high = $tasks->where('priority', 'high')->count();
        $msg = "Ada $count tugas hari ini. ";
        if ($high > 0) $msg .= "$high di antaranya bersifat kritikal ⚡. ";
        
        // Combo encouragement
        $combo = $user->combo_count ?? 0;
        if ($combo >= 3) $msg .= "Combo saat ini: {$combo}x 🔥 Pertahankan! ";
        
        $avgZen = $user->zenSessions()->where('date', '>=', Carbon::today()->subDays(7))->avg('duration_minutes') ?? 0;
        if ($avgZen > 30) $msg .= "Fokusmu minggu ini luar biasa, pertahankan ritme Zen-mu!";
        else $msg .= "Ingat gunakan Zen Mode untuk tugas yang butuh konsentrasi tinggi.";
        
        return $msg;
    }

    private function getAdvancedHeatmap($user)
    {
        $data = Schedule::where('user_id', $user->id)
            ->where('is_completed', true)
            ->where('date', '>=', Carbon::today()->subMonths(3))
            ->select('date', DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        $heatmap = [];
        for ($i = 90; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $heatmap[] = [
                'date' => $date,
                'count' => $data[$date] ?? 0
            ];
        }
        return $heatmap;
    }

    private function getZenCorrelation($user)
    {
        // Simple correlation logic for demo
        $zenTime = $user->zenSessions()->sum('duration_minutes');
        $tasksDone = Schedule::where('user_id', $user->id)->where('is_completed', true)->count();
        
        return [
            'zen_total' => $zenTime,
            'efficiency' => $zenTime > 0 ? round($tasksDone / ($zenTime / 60), 1) : 0
        ];
    }

    public function store(Request $request) {
        $request->validate([
            'activity_name' => 'required|string|min:3',
            'category'      => 'required',
            'notes'         => 'nullable|string|max:1000',
            'date'          => 'required|date',
            'time'          => 'required',
            'group_id'      => 'nullable|exists:groups,id',
            'attachment_file'=> 'nullable|file|max:5120', // Max 5MB
            'attachment_type'=> 'nullable|string',
            'dependency_id' => 'nullable|exists:schedules,id'
        ]);

        $data = $request->except(['attachment_file']);
        $data['user_id'] = auth()->id();
        $data['user_name'] = auth()->user()->name;
        $data['is_completed'] = false;

        if ($request->hasFile('attachment_file')) {
            $file = $request->file('attachment_file');
            $path = $file->store('attachments', 'public');
            $data['attachment_file'] = $path;
            if (!$request->attachment_type) {
                $ext = strtolower($file->getClientOriginalExtension());
                if (in_array($ext, ['pdf'])) $data['attachment_type'] = 'PDF';
                elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) $data['attachment_type'] = 'Gambar';
                elseif (in_array($ext, ['mp4', 'mov', 'avi'])) $data['attachment_type'] = 'Video';
                else $data['attachment_type'] = 'Dokumen';
            }
        }

        $schedule = Schedule::create($data);

        if ($request->group_id) {
            $group = \App\Models\Group::find($request->group_id);
            foreach ($group->members as $member) {
                if ($member->id !== auth()->id()) {
                    $clone = $schedule->replicate();
                    $clone->user_id = $member->id;
                    $clone->user_name = $member->name;
                    $clone->save();
                    $member->notify(new \App\Notifications\GeneralNotification("🤝 Tugas Grup Baru", "Admin " . auth()->user()->name . " menambahkan tugas '" . $schedule->activity_name . "' ke grup.", "🤝"));
                }
            }
        }

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan! 📅 Data otomatis tampil di Kalender & Kanban Board.');
    }

    // 5. Hapus Jadwal
    public function destroy($id) {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            return redirect()->back()->withErrors('Data tidak ditemukan');
        }
        $schedule->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus!');
    }

    // 6. Snooze All Today (FITUR BARU)
    public function snoozeAllToday() {
        $today = Carbon::today()->format('Y-m-d');
        
        $schedules = Schedule::where('date', $today)
                             ->where('is_completed', false)
                             ->get();
                             
        $count = 0;
        foreach($schedules as $item) {
            $scheduleTime = Carbon::parse($item->date . ' ' . $item->time);
            
            // Hanya tunda jika belum terlewat
            if (!$scheduleTime->isPast()) {
                $item->time = $scheduleTime->addHours(2)->format('H:i');
                $item->save();
                $count++;
            }
        }

        return redirect()->back()->with('success', "🚨 $count jadwal hari ini berhasil diundur 2 jam karena kegiatan mendadak!");
    }

    public function suggestTime(Request $request) {
        $user = auth()->user();
        $date = $request->date ?? date('Y-m-d');
        
        $busyTimes = Schedule::where('user_id', $user->id)
                             ->where('date', $date)
                             ->pluck('time')
                             ->map(fn($t) => substr($t, 0, 5))
                             ->toArray();
                             
        // Slot waktu dari jam 08:00 sampai 20:00
        $slots = ["08:00", "09:00", "10:00", "11:00", "13:00", "14:00", "15:00", "16:00", "17:00", "19:00", "20:00"];
        
        foreach($slots as $slot) {
            if (!in_array($slot, $busyTimes)) {
                return response()->json(['suggested_time' => $slot]);
            }
        }
        
        return response()->json(['suggested_time' => "21:00"]);
    }

    // Sub-task methods moved to SubTaskController

    public function calendar(Request $request) {
        if ($request->ajax()) {
            $user = auth()->user();
            $query = Schedule::query();
            if ($user->role !== 'admin') {
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('group', function($g) use ($user) {
                          $g->whereHas('members', function($m) use ($user) {
                              $m->where('users.id', $user->id);
                          });
                      });
                });
            }
            $schedules = $query->get();
            
            $events = $schedules->map(function($s) {
                $isCompleted = $s->is_completed;
                $isEarly = false;
                if ($isCompleted && $s->completed_at) {
                    $deadline = Carbon::parse($s->date . ' ' . $s->time);
                    $isEarly = Carbon::parse($s->completed_at)->lt($deadline);
                }
                $isMissed = !$isCompleted && Carbon::parse($s->date . ' ' . $s->time)->isPast();
                
                // Color coding: green = done, gold = early done, red = missed/high priority, purple = default
                if ($isCompleted && $isEarly) {
                    $color = '#F59E0B'; // Gold for early completion
                } elseif ($isCompleted) {
                    $color = '#10b981'; // Green for completed
                } elseif ($isMissed) {
                    $color = '#EF4444'; // Red for missed
                } elseif ($s->priority == 'high') {
                    $color = '#EF4444'; // Red for high priority
                } else {
                    $color = '#6366f1'; // Purple for default
                }

                $title = $s->activity_name;
                if ($isCompleted && $isEarly) $title = '🏃 ' . $title;
                elseif ($isCompleted) $title = '✅ ' . $title;
                elseif ($isMissed) $title = '⚠️ ' . $title;

                return [
                    'id' => $s->id,
                    'title' => $title,
                    'start' => $s->date . 'T' . $s->time,
                    'color' => $color,
                    'extendedProps' => [
                        'category' => $s->category,
                        'priority' => $s->priority,
                        'is_completed' => $isCompleted,
                        'is_early' => $isEarly,
                        'notes' => $s->notes,
                        'group_name' => $s->group_name,
                    ]
                ];
            });
            return response()->json($events);
        }
        return view('schedules.calendar');
    }

    public function markNotificationsAsRead() {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }
}