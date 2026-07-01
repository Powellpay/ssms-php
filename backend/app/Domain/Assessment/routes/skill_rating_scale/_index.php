<?php

use App\Domain\Assessment\Controllers\SkillRatingScaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('skill-rating-scale', SkillRatingScaleController::class);
});
