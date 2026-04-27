<?php

use Illuminate\Support\Facades\Route;
use Modules\Dosen\Http\Controllers\DosenController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('dosens', DosenController::class)->names('dosen');
});
