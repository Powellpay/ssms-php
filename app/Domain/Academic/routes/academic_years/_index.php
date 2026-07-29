<?php

use App\Domain\Academic\Controllers\AcademicYearController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('academic-years/current', [AcademicYearController::class, 'current']);
    Route::post('academic-years/{id}/set-current', [AcademicYearController::class, 'setCurrent']);
    Route::apiResource('academic-years', AcademicYearController::class);
});
