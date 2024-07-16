<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        Auth::logout();
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request)
    {
        Log::info('Request data:', $request->all());
        $validatedData = $request->validated();

        Log::info('Validation passed');
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        Log::info('User created:', $user->toArray());
        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
    public function sendEmailCode()
    {
        $email = Auth::user()->email;
        $code = rand(100000, 999999);
        $expireTime = 60 * 3;
        $EMAIL_VERIFY_CODE = "email:verify:code:" . $email;
        Cache::put($EMAIL_VERIFY_CODE, $code, $expireTime);
        Mail::send("emails.verification", ["code" => $code, "email" => $email], function (Message $message) use ($email) {
            $message->to($email);
            $message->subject("dcat驗證信件");
        });

        return response()->json(['message' => '驗證碼已發送']);
    }
    public function verify(Request $request)
    {
        $email = Auth::user()->email;
        $request->validate(['code' => 'required']);

        $EMAIL_VERIFY_CODE = "email:verify:code:" . $email;
        $isCode = Cache::get($EMAIL_VERIFY_CODE);

        if ($isCode && $isCode == $request->code) {
            return response()->json(['message' => '驗證成功']);
            // return redirect(RouteServiceProvider::HOME);
        }

        return response()->json(['message' => '驗證失敗'], 400);
    }
}
