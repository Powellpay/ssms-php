<?php

use App\Domain\Auth\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('roles', RoleController::class);
});
