<?php

use App\Domain\Announcements\Controllers\AnnouncementController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('announcements', AnnouncementController::class);
});
