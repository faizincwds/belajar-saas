<?php

use Core\Decrypt;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('test');
});

use App\Events\TestEvent;

Route::get('/test', function () {
    event(new TestEvent("Halo dari Laravel Reverb!"));
    return "Event sent!";
});

