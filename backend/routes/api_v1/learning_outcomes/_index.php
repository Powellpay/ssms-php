<?php

use App\Http\Controllers\Api\LearningOutcomeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('learning-outcomes', LearningOutcomeController::class);
});
