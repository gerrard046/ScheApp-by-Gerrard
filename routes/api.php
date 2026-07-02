<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ScheduleController;

/*
|--------------------------------------------------------------------------
| API Routes (untuk mobile client — Android)
|--------------------------------------------------------------------------
| Auth memakai Laravel Sanctum (Bearer token), TERPISAH dari web yang
| tetap session-based. Semua endpoint data wajib melewati auth:sanctum —
| tanpa token valid, respons 401 Unauthorized.
*/

// ---- Publik (tanpa token) ----
Route::post('/login', [AuthController::class, 'login']);

// ---- Terproteksi (wajib header: Authorization: Bearer <token>) ----
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('schedules', ScheduleController::class);
    Route::patch('schedules/{id}/toggle', [ScheduleController::class, 'toggleComplete']);
});
