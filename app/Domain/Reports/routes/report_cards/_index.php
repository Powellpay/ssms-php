<?php

use App\Domain\Reports\Controllers\ReportCardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('report-cards/generate', [ReportCardController::class, 'generate']);
    Route::get('report-cards/{id}/pdf', [ReportCardController::class, 'downloadPdf']);
    Route::apiResource('report-cards', ReportCardController::class);
});
