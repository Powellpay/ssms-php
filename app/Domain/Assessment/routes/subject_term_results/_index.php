<?php

use App\Domain\Assessment\Controllers\SubjectTermResultController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('subject-term-results', SubjectTermResultController::class);
});
