<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load(['zenSessions']);

        // Stats
        $totalTasks = Schedule::where('user_id', $user->id)->count();
        $completedTasks = Schedule::where('user_id', $user->id)->where('is_completed', true)->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $zenTotal = $user->zenSessions()->sum('duration_minutes');

        // Early completions
        $earlyCompletions = $user->total_early_completions ?? 0;

        // Category breakdown
        $categoryBreakdown = Schedule::where('user_id', $user->id)
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        // Weekly productivity (last 7 days)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyData[] = [
                'day' => $date->format('D'),
                'date' => $date->format('Y-m-d'),
                'completed' => Schedule::where('user_id', $user->id)
                    ->where('is_completed', true)
                    ->where('date', $date->format('Y-m-d'))
                    ->count(),
                'total' => Schedule::where('user_id', $user->id)
                    ->where('date', $date->format('Y-m-d'))
                    ->count(),
            ];
        }

        // Achievements - now using the badge system
        $achievements = $this->calculateAchievements($user, $totalTasks, $completedTasks, $zenTotal);
        
        // Title system
        $title = $user->title ?? $this->getTitleForLevel($user->level);
        
        // Gamification stats
        $gamificationStats = [
            'combo' => $user->combo_count ?? 0,
            'highest_combo' => $user->highest_combo ?? 0,
            'total_early' => $earlyCompletions,
            'total_xp_earned' => $user->total_xp_earned ?? 0,
            'title' => $title,
            'badges_count' => count($user->badges ?? []),
        ];

        return view('schedules.profile', compact(
            'user', 'totalTasks', 'completedTasks', 'completionRate',
            'zenTotal', 'categoryBreakdown', 'weeklyData', 'achievements',
            'earlyCompletions', 'gamificationStats', 'title'
        ));
    }

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

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:255',
            'avatar_color' => 'nullable|string|max:7',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->bio = $request->bio;
        $user->avatar_color = $request->avatar_color ?? '#1E88E5';
        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    private function calculateAchievements($user, $totalTasks, $completedTasks, $zenTotal)
    {
        $earnedBadges = $user->badges ?? [];
        $earlyCount = $user->total_early_completions ?? 0;
        
        $achievements = [
            // Task milestones
            ['icon' => '🌟', 'name' => 'Langkah Pertama', 'desc' => 'Selesaikan tugas pertamamu', 'unlocked' => in_array('first_task', $earnedBadges) || $completedTasks >= 1, 'category' => 'milestone'],
            ['icon' => '✨', 'name' => 'Mulai Konsisten', 'desc' => 'Selesaikan 5 tugas', 'unlocked' => in_array('five_tasks', $earnedBadges) || $completedTasks >= 5, 'category' => 'milestone'],
            ['icon' => '🔥', 'name' => 'Pekerja Keras', 'desc' => 'Selesaikan 10 tugas', 'unlocked' => in_array('ten_tasks', $earnedBadges) || $completedTasks >= 10, 'category' => 'milestone'],
            ['icon' => '⚡', 'name' => 'Quarter Master', 'desc' => 'Selesaikan 25 tugas', 'unlocked' => in_array('twenty_five', $earnedBadges) || $completedTasks >= 25, 'category' => 'milestone'],
            ['icon' => '💎', 'name' => 'Diamond Hustle', 'desc' => 'Selesaikan 50 tugas', 'unlocked' => in_array('fifty_tasks', $earnedBadges) || $completedTasks >= 50, 'category' => 'milestone'],
            ['icon' => '👑', 'name' => 'Century Club', 'desc' => 'Selesaikan 100 tugas', 'unlocked' => in_array('century', $earnedBadges) || $completedTasks >= 100, 'category' => 'milestone'],
            
            // Early completion
            ['icon' => '🐦', 'name' => 'Early Bird', 'desc' => '5 tugas sebelum tenggat', 'unlocked' => in_array('early_bird', $earnedBadges) || $earlyCount >= 5, 'category' => 'speed'],
            ['icon' => '⚡', 'name' => 'Speed Demon', 'desc' => '20 tugas sebelum tenggat', 'unlocked' => in_array('speed_demon', $earnedBadges) || $earlyCount >= 20, 'category' => 'speed'],
            ['icon' => '⏳', 'name' => 'Time Lord', 'desc' => '50 tugas sebelum tenggat', 'unlocked' => in_array('time_lord', $earnedBadges) || $earlyCount >= 50, 'category' => 'speed'],
            
            // Streaks
            ['icon' => '🔥', 'name' => '3 Hari Berturut', 'desc' => 'Raih 3 hari streak', 'unlocked' => in_array('streak_3', $earnedBadges) || $user->streak >= 3, 'category' => 'streak'],
            ['icon' => '💪', 'name' => 'Unstoppable', 'desc' => 'Raih 7 hari streak', 'unlocked' => in_array('streak_7', $earnedBadges) || $user->streak >= 7, 'category' => 'streak'],
            ['icon' => '🏅', 'name' => 'Iron Will', 'desc' => 'Raih 30 hari streak', 'unlocked' => in_array('streak_30', $earnedBadges) || $user->streak >= 30, 'category' => 'streak'],
            
            // Combos
            ['icon' => '🎯', 'name' => 'Combo Master', 'desc' => 'Raih combo 5x', 'unlocked' => in_array('combo_5', $earnedBadges) || ($user->highest_combo ?? 0) >= 5, 'category' => 'combo'],
            ['icon' => '💥', 'name' => 'Unstoppable Combo', 'desc' => 'Raih combo 10x', 'unlocked' => in_array('combo_10', $earnedBadges) || ($user->highest_combo ?? 0) >= 10, 'category' => 'combo'],

            // Levels
            ['icon' => '⚡', 'name' => 'Power User', 'desc' => 'Capai Level 5', 'unlocked' => in_array('level_5', $earnedBadges) || $user->level >= 5, 'category' => 'level'],
            ['icon' => '🏆', 'name' => 'Legend', 'desc' => 'Capai Level 10', 'unlocked' => in_array('level_10', $earnedBadges) || $user->level >= 10, 'category' => 'level'],
            ['icon' => '🐉', 'name' => 'Mythical', 'desc' => 'Capai Level 20', 'unlocked' => in_array('level_20', $earnedBadges) || $user->level >= 20, 'category' => 'level'],

            // Zen
            ['icon' => '🧘', 'name' => 'Zen Master', 'desc' => 'Total 60 menit Zen Mode', 'unlocked' => in_array('zen_1h', $earnedBadges) || $zenTotal >= 60, 'category' => 'zen'],
            ['icon' => '🧊', 'name' => 'Deep Focus', 'desc' => 'Total 300 menit Zen Mode', 'unlocked' => in_array('zen_5h', $earnedBadges) || $zenTotal >= 300, 'category' => 'zen'],
        ];

        return $achievements;
    }
}
