<?php

use App\Http\Controllers\Api\TimetableController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('timetable', TimetableController::class);
});
