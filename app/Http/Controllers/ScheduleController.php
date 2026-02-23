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

        $stats = [
            'total' => $allSchedules->count(),
            'today' => $todayTasks->count(),
            'completion_rate' => $allSchedules->count() > 0 ? round(($allSchedules->where('is_completed', true)->count() / $allSchedules->count()) * 100) : 0,
            'xp' => $user->xp,
            'level' => $user->level,
            'streak' => $user->streak,
            'xp_next' => $user->level * 100,
            'heatmap' => $heatmap,
            'zen_correlation' => $zenCorrelation,
            'briefing' => $briefing
        ];
        
        $groups = $user->administeredGroups->merge($user->groups)->unique('id');

        return view('schedules.index', [
            'schedules' => $sortedSchedules, 
            'stats' => $stats,
            'groups' => $groups
        ]);
    }

    public function kanban()
    {
        $user = auth()->user();
        $query = Schedule::with(['group', 'parentTask']);
        
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $tasks = $query->get();
        
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

            $schedule->is_completed = !$schedule->is_completed;
            $schedule->is_verified = false;
            $schedule->save();

            if ($schedule->is_completed) {
                $user->xp += 10;
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                
                if ($user->last_activity_date == $yesterday) $user->streak += 1;
                elseif ($user->last_activity_date != $today) $user->streak = 1;
                $user->last_activity_date = $today;

                if ($user->xp >= ($user->level * 100)) {
                    $user->level += 1;
                    $user->save();
                    return redirect()->back()->with('levelup', 'Level Up! LVL ' . $user->level);
                }
                $user->save();
                return redirect()->back()->with('success', 'Tugas selesai!');
            } else {
                $user->xp = max(0, $user->xp - 10);
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

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan!');
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
                $query->where('user_id', $user->id);
            }
            $schedules = $query->get();
            
            $events = $schedules->map(function($s) {
                return [
                    'id' => $s->id,
                    'title' => $s->activity_name,
                    'start' => $s->date . 'T' . $s->time,
                    'color' => $s->is_completed ? '#10b981' : ($s->priority == 'high' ? '#ef4444' : '#6366f1')
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