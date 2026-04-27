<?php

use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Http\Controllers\KeuanganController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('keuangans', KeuanganController::class)->names('keuangan');
});
