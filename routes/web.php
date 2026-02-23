<?php

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Halaman Depan langsung ke daftar
Route::get('/', function () {
    return redirect('/schedules');
});

use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\IsAdmin;

// Rute Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Semua rute schedule butuh login (middleware auth)
Route::middleware(['auth'])->group(function () {
    
    // Semua role bisa melihat jadwal (Menggunakan AI Smart Sorting dari tahap sebelumnya)
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/calendar', [ScheduleController::class, 'calendar']);
    Route::get('/groups', [GroupController::class, 'index']);

    // Rute manipulasi jadwal hanya untuk ADMIN
    Route::middleware([IsAdmin::class])->group(function () {
        Route::get('/schedules/suggest', [ScheduleController::class, 'suggestTime']);
        Route::post('/schedules', [ScheduleController::class, 'store']);
        Route::post('/schedules/snooze', [ScheduleController::class, 'snoozeAllToday']);
        Route::patch('/schedules/{id}/toggle', [ScheduleController::class, 'toggleComplete']);
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
        
        // Sub-Task Routes
        Route::post('/sub-tasks', [ScheduleController::class, 'storeSubTask']);
        Route::post('/sub-tasks/{id}/toggle', [ScheduleController::class, 'toggleSubTask']);

        // Group Management
        Route::post('/groups', [GroupController::class, 'store']);
        Route::post('/groups/{id}/add-member', [GroupController::class, 'addMember']);

        // Master Analytics
        Route::get('/admin/insights', [AdminController::class, 'insights']);
    });
});