<?php

use App\Http\Controllers\Api\GuardianController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('guardians', GuardianController::class);
});
