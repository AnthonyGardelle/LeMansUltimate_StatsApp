<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\{
    AuthController,
    UserController,
    ResultController,
    LocaleController,
    XmlUploadController
};
use App\Http\Controllers\Auth\{
    ForgotPasswordController,
    ResetPasswordController
};

// Page d'accueil
Route::view('/', 'welcome')->name('home');

// Routes pour les invités (non authentifiés)
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::view('/register', 'auth.register')->name('show.register');
    Route::view('/login', 'auth.login')->name('show.login');
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
});

// Routes pour les utilisateurs authentifiés
Route::middleware('auth')->group(function () {
    // Profil utilisateur
    Route::controller(UserController::class)->group(function () {
        Route::get('/profile', function () {
            $userId = auth()->id();
            $progress = Cache::get("upload_progress_$userId", 0);
            $total = Cache::get("upload_total_$userId", 0);

            if ($progress >= $total && $total !== 0) {
                Cache::put("upload_progress_$userId", 0);
                Cache::put("upload_total_$userId", 0);
            }

            return view('user.profile');
        })->name('show.profile');

        Route::put('/profile/update', 'update')->name('profile.update');
    });

    // Upload XML
    Route::post('/upload-xml', [XmlUploadController::class, 'upload'])->name('upload.xml');

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Suivi de l'upload
    Route::get('/upload-progress', function () {
        $userId = auth()->id();

        return response()->json([
            'upload_progress' => Cache::get("upload_progress_$userId"),
            'upload_total' => Cache::get("upload_total_$userId"),
        ]);
    })->name('upload.progress');
});

// Localisation
Route::get('/locale/{lang}', [LocaleController::class, 'setLocale'])->name('locale.set');

// Mot de passe oublié - accessible sans authentification ET avec authentification
Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password', 'showLinkRequestForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::get('/reset-password/{token}', 'showResetForm')->name('password.reset');
    Route::post('/reset-password', 'reset')->name('password.update');
});