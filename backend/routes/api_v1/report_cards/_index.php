<?php

use App\Http\Controllers\Api\ReportCardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('report-cards', ReportCardController::class);
});
