<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    // 1. Ambil semua jadwal (Dengan deteksi jadwal terlewat)
    public function index()
    {
        $now = Carbon::now(); // Waktu sekarang (pastikan timezone di .env sudah Asia/Jakarta)

        $schedules = Schedule::orderBy('is_completed', 'asc')
                             ->orderBy('date', 'asc')
                             ->orderBy('time', 'asc')
                             ->get()
                             ->map(function ($item) use ($now) {
                                 // Gabungkan tanggal dan jam untuk dicek
                                 $scheduleTime = Carbon::parse($item->date . ' ' . $item->time);
                                 
                                 // Jika sudah lewat waktu dan BELUM dikerjakan, maka is_missed = true
                                 $item->is_missed = $scheduleTime->isPast() && !$item->is_completed;
                                 
                                 return $item;
                             });

        return response()->json([
            'status' => 'success',
            'total' => $schedules->count(),
            'current_time' => $now->toDateTimeString(),
            'data' => $schedules
        ], 200);
    }

    // 2. Simpan jadwal baru
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

    // 3. Update status Selesai (Checklist)
    public function toggleComplete($id)
    {
        $schedule = Schedule::find($id);
        
        if (!$schedule) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $schedule->is_completed = !$schedule->is_completed;
        $schedule->save();

        return response()->json([
            'status'       => 'success',
            'message'      => 'Status diperbarui!',
            'is_completed' => $schedule->is_completed,
            'data'         => $schedule
        ], 200);
    }

    // 4. Hapus jadwal
    public function destroy($id)
    {
        $schedule = Schedule::find($id);
        
        if ($schedule) {
            $schedule->delete();
            return response()->json([
                'status' => 'success', 
                'message' => 'Jadwal berhasil dihapus!'
            ], 200);
        }
        
        return response()->json([
            'status' => 'error', 
            'message' => 'Data tidak ditemukan'
        ], 404);
    }
}