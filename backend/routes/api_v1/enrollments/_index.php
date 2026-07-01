<?php

use App\Http\Controllers\Api\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('enrollments', EnrollmentController::class);
});
