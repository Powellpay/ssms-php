<?php

use App\Domain\Assessment\Controllers\AssessmentRecordController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('assessment-records', AssessmentRecordController::class);
});
