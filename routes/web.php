<?php

use App\Http\Controllers\ImageToTextController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::resource('image-to-text', ImageToTextController::class);
