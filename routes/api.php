<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;


Route::get('schedules/stats', [ScheduleController::class, 'statistics']);


Route::patch('schedules/{id}/toggle', [ScheduleController::class, 'toggleComplete']);


Route::apiResource('schedules', ScheduleController::class);