<?php

use App\Http\Controllers\Api\SubjectTeacherController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('subject-teachers', SubjectTeacherController::class);
});
