<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChirpController;

Route::post('/chirps', [ChirpController::class, 'store'])->middleware('auth');
Route::get('/', function () {
    return view('welcome');
});
