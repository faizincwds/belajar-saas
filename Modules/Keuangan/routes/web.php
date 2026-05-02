<?php

use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Http\Controllers\KeuanganController;

    Route::resource('keuangans', KeuanganController::class)->names('keuangan');

