<?php

use App\Http\Controllers\Api\SubjectTermResultController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('subject-term-results', SubjectTermResultController::class);
});
