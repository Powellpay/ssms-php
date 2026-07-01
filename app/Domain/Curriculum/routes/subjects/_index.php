<?php

use App\Domain\Curriculum\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('subjects', SubjectController::class);
});
