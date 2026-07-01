<?php

use App\Http\Controllers\Api\SubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('subjects', SubjectController::class);
});
