<?php

use App\Domain\Curriculum\Controllers\LearningOutcomeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('learning-outcomes', LearningOutcomeController::class);
});
