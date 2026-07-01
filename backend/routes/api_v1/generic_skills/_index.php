<?php

use App\Http\Controllers\Api\GenericSkillController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('generic-skills', GenericSkillController::class);
});
