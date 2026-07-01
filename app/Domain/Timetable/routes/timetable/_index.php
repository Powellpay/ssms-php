<?php

use App\Domain\Timetable\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('timetable', TimetableController::class);
});
