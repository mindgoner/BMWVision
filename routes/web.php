<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorInfoController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/sensor', [SensorInfoController::class, 'handle']);