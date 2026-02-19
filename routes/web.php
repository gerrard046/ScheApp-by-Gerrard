<?php

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Halaman Depan langsung ke daftar
Route::get('/', function () {
    return redirect('/schedules');
});

// Tampilan Web (Tanpa Auth agar tidak error)
Route::get('/schedules', function () {
    // Kita pakai try-catch agar jika database error, web tidak putih polos
    try {
        $schedules = Schedule::orderBy('date', 'asc')->get();
    } catch (\Exception $e) {
        $schedules = collect([]); // Data kosong jika db error
    }
    
    $stats = ['total' => $schedules->count(), 'today' => 0, 'groups' => 0];
    return view('schedules.index', compact('schedules', 'stats'));
});

// Hapus baris Auth::routes() di bawah ini!