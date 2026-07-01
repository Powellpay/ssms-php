<?php

use App\Http\Controllers\Api\FeeStructureController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('fee-structures', FeeStructureController::class);
});
