<?php

use App\Http\Controllers\Api\AssessmentRecordController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('assessment-records', AssessmentRecordController::class);
});
