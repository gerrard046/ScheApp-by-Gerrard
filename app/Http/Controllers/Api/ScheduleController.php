<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    // 1. Ambil semua jadwal untuk Android
    public function index()
    {
        // Urutkan: Yang belum selesai di atas, lalu berdasarkan waktu terbaru
        $schedules = Schedule::orderBy('is_completed', 'asc')
                             ->orderBy('date', 'desc')
                             ->orderBy('time', 'desc')
                             ->get();
                             
        return response()->json($schedules, 200);
    }

    // 2. Simpan jadwal baru dari Android
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name'    => 'required|string',
            'activity_name' => 'required|string',
            'date'          => 'required|date',
            'time'          => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $schedule = Schedule::create([
            'user_name'     => $request->user_name ?? 'Android_User',
            'group_name'    => $request->group_name,
            'activity_name' => $request->activity_name,
            'category'      => $request->category ?? 'Lainnya',
            'priority'      => $request->priority ?? 'med',
            'date'          => $request->date,
            'time'          => $request->time,
            'is_completed'  => false,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Jadwal berhasil dibuat!',
            'data'    => $schedule
        ], 201);
    }

    // 3. Update status Selesai (Checklist) - Dipanggil via api.php
    public function toggleComplete($id)
    {
        $schedule = Schedule::find($id);
        
        if (!$schedule) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $schedule->is_completed = !$schedule->is_completed;
        $schedule->save();

        return response()->json([
            'status'       => 'success',
            'message'      => 'Status diperbarui!',
            'is_completed' => $schedule->is_completed
        ], 200);
    }

    // 4. Hapus jadwal dari Android
    public function destroy($id)
    {
        $schedule = Schedule::find($id);
        
        if ($schedule) {
            $schedule->delete();
            return response()->json(['status' => 'success', 'message' => 'Jadwal dihapus!'], 200);
        }
        
        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
    }
}