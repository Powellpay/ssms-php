<?php

use App\Http\Controllers\Api\AssessmentTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('assessment-types', AssessmentTypeController::class);
});
