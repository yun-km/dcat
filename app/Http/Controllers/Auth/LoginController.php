<?php

namespace App\Http\Controllers\Auth;


use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Storage;
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

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'password' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();
        $user->api_token = Str::random(60);
        $user->save();

        return[
            'result' => 'success',
            'message' => __('Login success'),
            'content' => [
                'api_token' => $user->api_token,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar, 
                ],
            ],

        ];
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->api_token = null;
            $user->save();
 
            return[
                'result' => 'success',
                'message' => __('Logout successful'),
            ];
        }
    
        return [
            'result' => 'failure',
            'message' => __('Logout failed. No authenticated user found.'),
        ];
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

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        $path = $request->file('avatar')->store('images', 'avatars');

        if ($user->avatar) {
            Storage::disk('avatars')->delete($user->avatar);
        }

        $user->avatar = $path;
        $user->save();

        return back()->with('success',  __('auth.avatars update success'));
    }
}
