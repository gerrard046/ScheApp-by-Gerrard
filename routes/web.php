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
    Route::get('/kanban', [ScheduleController::class, 'kanban']);

    // Notifications
    Route::post('/notifications/read-all', [ScheduleController::class, 'markNotificationsAsRead']);
    Route::post('/zen/log', [ScheduleController::class, 'logZenSession']);

    // --- Core Schedule Management (Available to All Users) ---
    Route::post('/schedules', [ScheduleController::class, 'store']);
    Route::post('/schedules/{id}/toggle', [ScheduleController::class, 'toggleComplete']);
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
    Route::get('/schedules/suggest', [ScheduleController::class, 'suggestTime']);
    
    // Sub-Task Routes (Users can manage their own)
    Route::post('/schedules/{schedule}/sub-tasks', [\App\Http\Controllers\SubTaskController::class, 'store']);
    Route::post('/sub-tasks/{id}/toggle', [\App\Http\Controllers\SubTaskController::class, 'toggle']);
    Route::delete('/sub-tasks/{id}', [\App\Http\Controllers\SubTaskController::class, 'destroy']);

    // --- Admin-Only Powerful Features ---
    Route::middleware([IsAdmin::class])->group(function () {
        Route::post('/schedules/snooze', [ScheduleController::class, 'snoozeAllToday']);
        
        // Group Management (System-wide Admin)
        Route::post('/groups', [GroupController::class, 'store']);
        Route::post('/groups/{id}/add-member', [GroupController::class, 'addMember']);
        Route::post('/groups/{id}/resources', [GroupController::class, 'storeResource'])->name('groups.resources.store');
        Route::get('/groups/resources/{id}/download', [GroupController::class, 'downloadResource'])->name('groups.resources.download');

        // Master Analytics
        Route::get('/admin/insights', [AdminController::class, 'insights']);
        Route::get('/admin/insights/export', [AdminController::class, 'exportPdf'])->name('admin.insights.export');
    });
});