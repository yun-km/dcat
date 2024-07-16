<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     *
     */
    public function show(): View
    {
        Auth::logout();
        return view('auth.login');
    }

    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => __('auth.emailRequired'),
            'email.email' => __('auth.email_format'),
            'password.required' => __('auth.passwordRequired'),
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if (!Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')->withErrors([
                   'email' => __('auth.email_verification_required'),
                ]);
            }
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        throw ValidationException::withMessages([
            'password' => __('auth.failed'),
        ]);
    }
}
