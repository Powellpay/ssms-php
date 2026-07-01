<?php

use App\Http\Controllers\Api\BookLoanController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('book-loans', BookLoanController::class);
});
