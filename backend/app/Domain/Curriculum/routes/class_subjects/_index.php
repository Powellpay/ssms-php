<?php

use App\Domain\Curriculum\Controllers\ClassSubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('class-subjects', ClassSubjectController::class);
});
