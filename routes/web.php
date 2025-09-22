<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::resource('image-to-text', 'App\Http\Controllers\ImageToTextController');
