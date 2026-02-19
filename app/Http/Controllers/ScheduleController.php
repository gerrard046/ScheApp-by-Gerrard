<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    // 1. Ambil Semua Data + Statistik Ringkas
    public function index() {
        $now = Carbon::now();
        
        $allSchedules = Schedule::orderBy('date', 'asc')->orderBy('time', 'asc')->get();
        
        // Memetakan data agar punya status 'is_missed' secara otomatis
        $dataWithStatus = $allSchedules->map(function ($item) use ($now) {
            $scheduleTime = Carbon::parse($item->date . ' ' . $item->time);
            // Terlewat jika: Waktu sudah lewat DAN belum selesai
            $item->is_missed = $scheduleTime->isPast() && !$item->is_completed;
            return $item;
        });

        return response()->json([
            'status' => 'success',
            'current_time' => $now->toDateTimeString(),
            'stats' => [
                'total_jadwal' => $allSchedules->count(),
                'selesai' => $allSchedules->where('is_completed', true)->count(),
                'terlewat' => $dataWithStatus->where('is_missed', true)->count(),
                'mendatang' => $allSchedules->where('is_completed', false)->count(),
            ],
            'data' => $dataWithStatus
        ], 200);
    }

    // 2. FITUR BARU: Statistik Mendalam
    public function statistics() {
        $now = Carbon::now();
        $schedules = Schedule::all();
        
        $terlewat = $schedules->filter(function($item) use ($now) {
            return Carbon::parse($item->date . ' ' . $item->time)->isPast() && !$item->is_completed;
        })->count();

        return response()->json([
            'status' => 'success',
            'summary' => [
                'total' => $schedules->count(),
                'sudah_selesai' => $schedules->where('is_completed', true)->count(),
                'belum_selesai' => $schedules->where('is_completed', false)->count(),
                'terlewat_waktunya' => $terlewat,
                'persentase_selesai' => $schedules->count() > 0 
                    ? round(($schedules->where('is_completed', true)->count() / $schedules->count()) * 100, 2) . '%' 
                    : '0%'
            ]
        ], 200);
    }

    // 3. Simpan Jadwal Baru
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'activity_name' => 'required|string|min:3',
            'category'      => 'required',
            'date'          => 'required|date',
            'time'          => 'required',
            'group_name'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Default is_completed adalah false
        $data = $request->all();
        $data['is_completed'] = $data['is_completed'] ?? false;

        $schedule = Schedule::create($data);
        return response()->json(['status' => 'success', 'data' => $schedule], 201);
    }

    // 4. Update Status (Selesai/Belum)
    public function toggleComplete($id) {
        $schedule = Schedule::find($id);
        if (!$schedule) return response()->json(['status' => 'error'], 404);

        $schedule->is_completed = !$schedule->is_completed;
        $schedule->save();

        return response()->json(['status' => 'success', 'is_completed' => $schedule->is_completed], 200);
    }

    // 5. Hapus Jadwal
    public function destroy($id) {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }
        $schedule->delete();
        return response()->json(['status' => 'success', 'message' => 'Jadwal berhasil dihapus'], 200);
    }
}