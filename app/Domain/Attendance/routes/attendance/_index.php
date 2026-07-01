<?php

use App\Domain\Attendance\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('attendance', AttendanceController::class);
});
