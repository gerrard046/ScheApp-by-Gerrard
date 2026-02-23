<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function insights()
    {
        $data = $this->getInsightsData();
        return view('schedules.insights', $data);
    }

    public function exportPdf()
    {
        $data = $this->getInsightsData();
        $data['export_date'] = Carbon::now()->format('d F Y, H:i');
        
        $pdf = Pdf::loadView('admin.insights_pdf', $data);
        return $pdf->download('ScheApp_Pro_Insights_Report.pdf');
    }

    private function getInsightsData()
    {
        $totalUsers = User::count();
        $totalSchedules = Schedule::count();
        $completedSchedules = Schedule::where('is_completed', true)->count();
        $globalCompletionRate = $totalSchedules > 0 ? round(($completedSchedules / $totalSchedules) * 100) : 0;

        $topUsers = User::orderByDesc('xp')->take(10)->get();

        $today = date('Y-m-d');
        $todaySchedules = Schedule::where('date', $today)->count();
        $todayDone = Schedule::where('date', $today)->where('is_completed', true)->count();

        // Burnout risk users (more than 3 missed tasks in last 7 days)
        $sevenDaysAgo = Carbon::today()->subDays(7)->format('Y-m-d');
        $riskUsers = User::whereHas('schedules', function($q) use ($sevenDaysAgo) {
            $q->where('date', '>=', $sevenDaysAgo)
              ->where('is_completed', false)
              ->where(function($sq) {
                  $sq->where('date', '<', date('Y-m-d'))
                    ->orWhere(function($ssq) {
                        $ssq->where('date', date('Y-m-d'))
                           ->where('time', '<', date('H:i:s'));
                    });
              });
        })->withCount(['schedules' => function($q) use ($sevenDaysAgo) {
            $q->where('date', '>=', $sevenDaysAgo)
              ->where('is_completed', false)
              ->where(function($sq) {
                $sq->where('date', '<', date('Y-m-d'))
                  ->orWhere(function($ssq) {
                      $ssq->where('date', date('Y-m-d'))
                         ->where('time', '<', date('H:i:s'));
                  });
              });
        }])->get()->filter(fn($u) => $u->schedules_count >= 3);

        return [
            'totalUsers' => $totalUsers,
            'totalSchedules' => $totalSchedules,
            'globalCompletionRate' => $globalCompletionRate,
            'topUsers' => $topUsers,
            'todaySchedules' => $todaySchedules,
            'todayDone' => $todayDone,
            'riskUsers' => $riskUsers
        ];
    }
}
