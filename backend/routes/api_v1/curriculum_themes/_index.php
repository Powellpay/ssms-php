<?php

use App\Http\Controllers\Api\CurriculumThemeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('curriculum-themes', CurriculumThemeController::class);
});
