<?php

use App\Domain\Academic\Controllers\StreamController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('streams', StreamController::class);
});
