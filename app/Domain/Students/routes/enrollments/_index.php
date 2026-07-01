<?php

use App\Domain\Students\Controllers\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('enrollments', EnrollmentController::class);
});
