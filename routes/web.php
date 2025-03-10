<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResultController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/results', [ResultController::class, 'loadAllResult'])->name('results');

Route::post('/update-path', function (Request $request) {
    $request->validate(['results_path' => 'required|string']);

    session(['results_path' => $request->results_path]);

    return redirect()->back()->with('success', 'Chemin mis à jour avec succès !');
})->name('updatePath');

Route::get('/register', [AuthController::class, 'showregister'])->name('show.register');
Route::get('/login', [AuthController::class, 'showlogin'])->name('show.login');

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');