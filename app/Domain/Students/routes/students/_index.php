<?php

use App\Domain\Students\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('students', StudentController::class);
    Route::get('students/admission/{admissionNo}', [StudentController::class, 'findByAdmissionNo']);
    Route::get('students/status/{status}', [StudentController::class, 'findByStatus']);
    Route::get('students/{id}/enrollment', [StudentController::class, 'currentEnrollment']);
});
