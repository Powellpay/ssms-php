<?php

use App\Domain\Staff\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('staff', StaffController::class);
});
