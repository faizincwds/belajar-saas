<?php

use Modules\Dosen\Http\Controllers\JadwalController;
use Illuminate\Support\Facades\Route;
use Modules\Dosen\Http\Controllers\DosenController;

    Route::resource('dosens', DosenController::class)->names('dosen');

Route::resource('jadwal', JadwalController::class)->names('jadwal');
