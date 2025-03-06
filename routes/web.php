<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResultController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/results', [ResultController::class, 'loadAllResult'])->name('results');
