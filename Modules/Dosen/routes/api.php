<?php

use Illuminate\Support\Facades\Route;
use Modules\Dosen\Http\Controllers\DosenController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('dosens', DosenController::class)->names('dosen');
});
