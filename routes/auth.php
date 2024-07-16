<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
            ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store'])
                ->name('register.store');

    Route::get('login', [LoginController::class, 'show'])
                ->name('login');

    Route::post('login', [LoginController::class, 'authenticate'])
                ->name('login.authenticate');
});

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/verify-email',[RegisteredUserController::class, 'verify'])
                ->name('verification.verify-email');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect('/home');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/resend',[RegisteredUserController::class, 'sendEmailCode'])
                ->middleware(['throttle:6,1'])->name('verification.resend');

});
