<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Events\TestEvent;

Route::get('/test', function () {
    event(new TestEvent("Halo dari Laravel Reverb!"));
    return "Event sent!";
});

