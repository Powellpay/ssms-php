<?php

use App\Domain\Assessment\Controllers\GenericSkillRatingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('generic-skill-ratings', GenericSkillRatingController::class);
});
