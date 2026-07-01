<?php

use App\Domain\Assessment\Controllers\AssessmentTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('assessment-types', AssessmentTypeController::class);
});
