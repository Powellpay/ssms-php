<?php

use App\Domain\Reports\Controllers\ReportCardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('report-cards', ReportCardController::class);
});
