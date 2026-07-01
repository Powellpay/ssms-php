<?php

use App\Domain\Finance\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('payments', PaymentController::class);
});
