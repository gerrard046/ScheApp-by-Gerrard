<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ScheduleController;

Route::apiResource('schedules', ScheduleController::class);
Route::patch('schedules/{id}/toggle', [ScheduleController::class, 'toggleComplete']);