<?php

use App\Domain\Curriculum\Controllers\SubjectTeacherController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('subject-teachers', SubjectTeacherController::class);
});
