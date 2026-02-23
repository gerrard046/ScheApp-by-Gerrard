<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    // 1. Ambil Semua Data + Statistik Ringkas & AI Sorting
    public function index(Request $request) {
        $now = Carbon::now();
        $user = auth()->user();
        
        $query = Schedule::with(['subTasks', 'group']);
        
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

        // Admin Insight Filter (if requested)
        if ($request->has('needs_verification') && $user->role === 'admin') {
            $query->where('is_completed', true)->where('is_verified', false);
        }
        
        $allSchedules = $query->get();
        
        // Memetakan data agar punya status 'is_missed' dan hitung bobot prioritas
        $dataWithStatus = $allSchedules->map(function ($item) use ($now) {
            $scheduleTime = Carbon::parse($item->date . ' ' . $item->time);
            
            // Terlewat jika: Waktu sudah lewat DAN belum selesai
            $item->is_missed = $scheduleTime->isPast() && !$item->is_completed;
            
            // Hitung bobot AI Priority Score
            $score = 0;
            if (!$item->is_completed && !$item->is_missed) {
                $priorityScores = ['high' => 50, 'med' => 30, 'low' => 10];
                $score += $priorityScores[$item->priority] ?? 0;
                
                $hoursLeft = $now->diffInHours($scheduleTime, false);
                if ($hoursLeft >= 0 && $hoursLeft <= 24) {
                    $score += (24 - $hoursLeft) * 2;
                }
            }
            $item->ai_score = $score;
            return $item;
        });

        $sortedSchedules = $dataWithStatus->sortByDesc(function ($item) {
            if ($item->is_completed) return -1000;
            if ($item->is_missed) return -500;
            return $item->ai_score;
        })->values();

        // Stats for Analytics
        $totalSchedules = $allSchedules->count();
        $completedSchedules = $allSchedules->where('is_completed', true)->count();
        $completionRate = $totalSchedules > 0 ? round(($completedSchedules / $totalSchedules) * 100) : 0;
        
        // Heatmap Data (Last 7 days)
        $heatmap = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $heatmap[] = [
                'date' => $date,
                'count' => $allSchedules->where('date', $date)->where('is_completed', true)->count()
            ];
        }

        $insightMessage = null;
        if ($allSchedules->where('is_missed', true)->count() >= 3) {
            $insightMessage = "⚠ Burnout Alert: Kamu punya banyak tugas terlewat. Fokus dulu ya!";
        }

        $stats = [
            'total' => $totalSchedules,
            'today' => $allSchedules->where('date', date('Y-m-d'))->where('is_completed', false)->count(),
            'completion_rate' => $completionRate,
            'xp' => $user->xp,
            'level' => $user->level,
            'streak' => $user->streak,
            'xp_next' => $user->level * 100,
            'heatmap' => $heatmap
        ];
        
        $groups = $user->administeredGroups->merge($user->groups)->unique('id');

        return view('schedules.index', [
            'schedules' => $sortedSchedules, 
            'stats' => $stats,
            'insightMessage' => $insightMessage,
            'groups' => $groups
        ]);
    }

    // 3. Simpan Jadwal Baru (Support Broadcasting)
    public function store(Request $request) {
        $request->validate([
            'activity_name' => 'required|string|min:3',
            'category'      => 'required',
            'date'          => 'required|date',
            'time'          => 'required',
            'group_id'      => 'nullable|exists:groups,id',
            'attachment_file'=> 'nullable|file|max:5120', // Max 5MB
            'attachment_type'=> 'nullable|string'
        ]);

        $data = $request->except(['attachment_file']);
        $data['user_id'] = auth()->id();
        $data['user_name'] = auth()->user()->name;
        $data['is_completed'] = false;

        // Handle File Upload for Attachment
        if ($request->hasFile('attachment_file')) {
            $file = $request->file('attachment_file');
            $path = $file->store('attachments', 'public');
            $data['attachment_file'] = $path;
            
            // Auto-detect type if not provided
            if (!$request->attachment_type) {
                $ext = strtolower($file->getClientOriginalExtension());
                if (in_array($ext, ['pdf'])) $data['attachment_type'] = 'PDF';
                elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) $data['attachment_type'] = 'Gambar';
                elseif (in_array($ext, ['mp4', 'mov', 'avi'])) $data['attachment_type'] = 'Video';
                else $data['attachment_type'] = 'Dokumen';
            }
        }

        $schedule = Schedule::create($data);

        // --- Broadcasting Logic ---
        if ($request->group_id) {
            $group = \App\Models\Group::find($request->group_id);
            foreach ($group->members as $member) {
                if ($member->id !== auth()->id()) {
                    $clone = $schedule->replicate();
                    $clone->user_id = $member->id;
                    $clone->user_name = $member->name;
                    $clone->save();

                    // Notify clones
                    $member->notify(new GeneralNotification(
                        "🤝 Tugas Grup Baru",
                        "Admin " . auth()->user()->name . " menambahkan tugas '" . $schedule->activity_name . "' ke grup.",
                        "🤝"
                    ));
                }
            }
        }

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    // 4. Update Status & Verification Flow
    public function toggleComplete(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $user = auth()->user();
        
        // If User toggles their own task
        if ($schedule->user_id === $user->id) {
            
            // Handle Proof Image Upload
            if ($request->hasFile('proof_image')) {
                $path = $request->file('proof_image')->store('proofs', 'public');
                $schedule->proof_image = $path;
            }

            $schedule->is_completed = !$schedule->is_completed;
            $schedule->is_verified = false; // Reset verification on toggle
            $schedule->save();

            if ($schedule->is_completed) {
                // Tugas Selesai: +10 XP
                $user->xp += 10;
                
                // Streak Logic
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                
                if ($user->last_activity_date == $yesterday) {
                    $user->streak += 1;
                } elseif ($user->last_activity_date != $today) {
                    $user->streak = 1;
                }
                $user->last_activity_date = $today;

                // Level Up Logic (Every 100 XP)
                if ($user->xp >= ($user->level * 100)) {
                    $user->level += 1;
                    $user->save();
                    return redirect()->back()->with('levelup', 'Selamat! Level Anda naik ke level ' . $user->level . '! Menunggu verifikasi Admin (+10 XP)');
                }
                
                $user->save();
                return redirect()->back()->with('success', 'Tugas selesai! Menunggu verifikasi Admin (+10 XP)');
            } else {
                // Tugas dibatalkan: -10 XP
                $user->xp = max(0, $user->xp - 10);
                $user->save();
            }
            return redirect()->back()->with('success', 'Status jadwal diperbarui.');
        }

        // If Admin verifies a user's task
        if ($user->role === 'admin' && $schedule->is_completed) {
            $schedule->is_verified = !$schedule->is_verified;
            $schedule->save();

            if ($schedule->is_verified) {
                $taskOwner = $schedule->user;
                if ($taskOwner) {
                    $taskOwner->xp += 5; // Bonus XP for verified task
                    $taskOwner->save();
                }

                // Notify User
                $taskOwner->notify(new GeneralNotification(
                    "✅ Tugas Diverifikasi",
                    "Admin telah memverifikasi tugas '" . $schedule->activity_name . "'. Berhasil dapat +5 XP Bonus!",
                    "✅"
                ));

                return redirect()->back()->with('success', 'Tugas berhasil diverifikasi! User mendapatkan +5 XP Bonus.');
            }
            return redirect()->back()->with('success', 'Verifikasi dibatalkan.');
        }

        return redirect()->back()->with('error', 'Akses ditolak.');
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