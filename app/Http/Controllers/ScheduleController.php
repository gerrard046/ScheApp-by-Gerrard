<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function index() {
        return response()->json([
            'status' => 'success',
            'total' => Schedule::count(),
            'data' => Schedule::orderBy('date', 'asc')->orderBy('time', 'asc')->get()
        ], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'activity_name' => 'required|string|min:3',
            'category'      => 'required',
            'date'          => 'required|date',
            'group_name'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $schedule = Schedule::create($request->all());
        return response()->json(['status' => 'success', 'data' => $schedule], 201);
    }

    // FITUR KECE: Hapus Jadwal
    public function destroy($id) {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }
        $schedule->delete();
        return response()->json(['status' => 'success', 'message' => 'Jadwal berhasil dihapus'], 200);
    }
}