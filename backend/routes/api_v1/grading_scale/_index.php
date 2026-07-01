<?php

use App\Http\Controllers\Api\GradingScaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('grading-scale', GradingScaleController::class);
});
