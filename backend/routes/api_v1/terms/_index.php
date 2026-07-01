<?php

use App\Http\Controllers\Api\TermController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('terms', TermController::class);
});
