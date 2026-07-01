<?php

use App\Http\Controllers\Api\SkillRatingScaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('skill-rating-scale', SkillRatingScaleController::class);
});
