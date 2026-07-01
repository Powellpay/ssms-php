<?php

use App\Http\Controllers\Api\ClassLevelController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('class-levels', ClassLevelController::class);
});
