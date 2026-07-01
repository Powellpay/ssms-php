<?php

use App\Http\Controllers\Api\LibraryBookController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('library-books', LibraryBookController::class);
});
