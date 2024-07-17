<?php

namespace App\Http\Controllers\Auth;


use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function resetPassword(Request $request) {
        $request->validate([
            'old_password' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ],[
            __('auth.OldPassword') => __("old_password"),
            __('auth.password') => __("password"),
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' =>  __('auth.The old password is incorrect.')]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('status',  __('auth.reset password success'));
    }
}
