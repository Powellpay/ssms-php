<?php

use App\Domain\Discipline\Controllers\DisciplineRecordController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('discipline-records', DisciplineRecordController::class);
});
