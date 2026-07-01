<?php

use App\Domain\Curriculum\Controllers\CurriculumThemeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('curriculum-themes', CurriculumThemeController::class);
});
