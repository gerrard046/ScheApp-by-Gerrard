<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;

Route::apiResource('schedules', ScheduleController::class);