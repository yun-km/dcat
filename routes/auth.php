<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('login', [LoginController::class, 'show'])
->name('login');
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store'])
                ->name('register.store');

    Route::post('/verification/sendEmail',[RegisteredUserController::class, 'sendEmailCode'])
                ->middleware(['throttle:6,1'])->name('verification.notice');


    Route::post('login', [LoginController::class, 'authenticate'])
                ->name('login.authenticate');
});

Route::middleware('auth')->group(function () {
    // reset password
    Route::get('/home', function () {
        return view('home');
    });

    Route::post('user/resetPassword', [LoginController::class, 'resetPassword'])
                ->name('user.resetPassword');

    Route::post('user/update-avatar', [LoginController::class, 'updateAvatar'])
                ->name('user.updateAvatar');
});
