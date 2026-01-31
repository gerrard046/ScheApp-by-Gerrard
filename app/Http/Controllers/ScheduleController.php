<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        // Ambil semua data
        $schedules = Schedule::orderBy('date', 'asc')->get();
        
        // Hitung Statistik
        $stats = [
            'total' => Schedule::count(),
            'today' => Schedule::whereDate('date', Carbon::today())->count(),
            'groups' => Schedule::distinct('group_name')->count('group_name')
        ];

        return view('schedules.index', compact('schedules', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'group_name' => 'required|string|max:255',
            'activity_name' => 'required|string|max:255',
            'category' => 'required|in:Belajar,Kerja,Meeting,Santai',
            'date' => 'required|date',
            'time' => 'nullable',
        ]);

        Schedule::create($validated);

        return redirect('/schedules')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return redirect('/schedules')->with('success', 'Jadwal berhasil dihapus!');
    }
}