<?php

use App\Http\Controllers\Api\DisciplineRecordController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('discipline-records', DisciplineRecordController::class);
});
