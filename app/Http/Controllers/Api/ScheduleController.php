<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * API Schedule untuk mobile client.
 *
 * Semua endpoint dilindungi auth:sanctum (lihat routes/api.php) dan
 * datanya di-scope ke user pemilik token — user tidak bisa membaca
 * atau mengubah jadwal milik orang lain (admin boleh semua).
 */
class ScheduleController extends Controller
{
    // 1. Ambil jadwal milik user login (dengan deteksi jadwal terlewat)
    public function index(Request $request)
    {
        $now  = Carbon::now();
        $user = $request->user();

        $query = Schedule::query();
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $schedules = $query->orderBy('is_completed', 'asc')
                           ->orderBy('date', 'asc')
                           ->orderBy('time', 'asc')
                           ->get()
                           ->map(function ($item) use ($now) {
                               $scheduleTime = Carbon::parse($item->date . ' ' . $item->time);
                               $item->is_missed = $scheduleTime->isPast() && !$item->is_completed;
                               return $item;
                           });

        return response()->json([
            'status'       => 'success',
            'total'        => $schedules->count(),
            'current_time' => $now->toDateTimeString(),
            'data'         => $schedules,
        ], 200);
    }

    // 2. Simpan jadwal baru (tercatat atas nama pemilik token)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activity_name' => 'required|string|min:3|max:255',
            'date'          => 'required|date',
            'time'          => 'required',
            'group_name'    => 'nullable|string|max:255',
            'category'      => 'nullable|string|max:100',
            'priority'      => 'nullable|in:low,med,high',
            'notes'         => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        $schedule = Schedule::create([
            'user_id'       => $user->id,
            'user_name'     => $user->name,
            'group_name'    => $request->group_name ?: 'Pribadi',
            'activity_name' => $request->activity_name,
            'category'      => $request->category ?? 'Lainnya',
            'priority'      => $request->priority ?? 'med',
            'notes'         => $request->notes,
            'date'          => $request->date,
            'time'          => $request->time,
            'is_completed'  => false,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Jadwal berhasil dibuat!',
            'data'    => $schedule,
        ], 201);
    }

    // 3. Toggle status selesai (hanya pemilik / admin)
    public function toggleComplete(Request $request, $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }
        if (!$this->canManage($request, $schedule)) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);
        }

        $schedule->is_completed = !$schedule->is_completed;
        $schedule->completed_at = $schedule->is_completed ? Carbon::now() : null;
        $schedule->save();

        return response()->json([
            'status'       => 'success',
            'message'      => 'Status diperbarui!',
            'is_completed' => $schedule->is_completed,
            'data'         => $schedule,
        ], 200);
    }

    // 4. Hapus jadwal (hanya pemilik / admin)
    public function destroy(Request $request, $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }
        if (!$this->canManage($request, $schedule)) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);
        }

        $schedule->delete();

        return response()->json(['status' => 'success', 'message' => 'Jadwal berhasil dihapus!'], 200);
    }

    private function canManage(Request $request, Schedule $schedule): bool
    {
        $user = $request->user();
        return $schedule->user_id === $user->id || $user->role === 'admin';
    }
}
