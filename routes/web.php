<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\XmlUploadController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::view('/register', 'auth.register')->name('show.register');
    Route::view('/login', 'auth.login')->name('show.login');
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
});

Route::middleware('auth')->controller(UserController::class)->group(function () {
    Route::get('/profile', function () {
        $userId = auth()->id();
        $progress = Cache::get('upload_progress_' . $userId, 0);
        $total = Cache::get('upload_total_' . $userId, 0);

        if ($progress >= $total && $total !== 0) {
            // Reset once everything is completed
            Cache::put('upload_progress_' . $userId, 0);
            Cache::put('upload_total_' . $userId, 0);
        }

        return view('user.profile');
    })->name('show.profile');
    Route::put('/profile/update', 'update')->name('profile.update');
});

Route::middleware('auth')->controller(ResultController::class)->group(function () {
    Route::get('/results', 'showResults')->name('results');
    Route::get('/results/{result}', 'showResult')->name('result');
});

Route::post('/upload-xml', [XmlUploadController::class, 'upload'])->name('upload.xml')->middleware('auth');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/locale/{lang}', action: [LocaleController::class, 'setLocale'])->name('locale.set');

Route::get('/upload-progress', function () {
    $userId = auth()->id();

    $data = [
        'upload_progress' => Cache::get('upload_progress_' . $userId),
        'upload_total' => Cache::get(key: 'upload_total_' . $userId),
    ];

    return response()->json($data);
})->name('upload.progress');
