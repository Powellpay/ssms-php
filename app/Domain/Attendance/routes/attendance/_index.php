<?php

use App\Domain\Attendance\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('attendance/register', [AttendanceController::class, 'register']);
    Route::get('attendance/register', [AttendanceController::class, 'registerShow']);
    Route::apiResource('attendance', AttendanceController::class);
});
