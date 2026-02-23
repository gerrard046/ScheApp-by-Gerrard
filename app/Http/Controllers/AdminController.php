<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function insights()
    {
        $totalUsers = User::count();
        $totalSchedules = Schedule::count();
        $completedSchedules = Schedule::where('is_completed', true)->count();
        $globalCompletionRate = $totalSchedules > 0 ? round(($completedSchedules / $totalSchedules) * 100) : 0;

        $topUsers = User::orderByDesc('xp')->take(5)->get();

        $today = date('Y-m-d');
        $todaySchedules = Schedule::where('date', $today)->count();
        $todayDone = Schedule::where('date', $today)->where('is_completed', true)->count();

        // Burnout risk users (more than 3 missed tasks in last 7 days)
        $sevenDaysAgo = Carbon::today()->subDays(7)->format('Y-m-d');
        $riskUsers = User::whereHas('schedules', function($q) use ($sevenDaysAgo) {
            $q->where('date', '>=', $sevenDaysAgo)
              ->where('is_completed', false)
              ->whereRaw('CONCAT(date, " ", time) < ?', [now()]);
        })->withCount(['schedules' => function($q) use ($sevenDaysAgo) {
            $q->where('date', '>=', $sevenDaysAgo)
              ->where('is_completed', false)
              ->whereRaw('CONCAT(date, " ", time) < ?', [now()]);
        }])->get()->filter(fn($u) => $u->schedules_count >= 3);

        return view('schedules.insights', compact(
            'totalUsers', 'totalSchedules', 'globalCompletionRate', 
            'topUsers', 'todaySchedules', 'todayDone', 'riskUsers'
        ));
    }
}
