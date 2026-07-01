<?php

use App\Domain\Finance\Controllers\FeeStructureController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('fee-structures', FeeStructureController::class);
});
