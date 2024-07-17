<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use App\Models\VerificationMailLog;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
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

        $latestLog = VerificationMailLog::where('email', $validatedData['email'])
                        ->orderBy('created_at', 'desc')
                        ->first();

        if (!$latestLog) {
            return back()->withErrors(['verification' => '此信箱尚未寄送驗證']);
        }

        $currentTime = Carbon::now();
        $expiresAt = Carbon::parse($latestLog->expires_at);

        if ($currentTime->greaterThan($expiresAt)) {
            return back()->withErrors(['verification' => '驗證碼過期']);
        }else if ($latestLog->verification_code !== $validatedData['verification']) {
            return back()->withErrors(['verification' => '驗證碼錯誤']);
        }

        $latestLog->update(['is_verified' => true]);

        $user = User::create([
            'name' => $validatedData['email'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'email_verified_at' => now()
        ]);

        Log::info('User created:', $user->toArray());
        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
    public function sendEmailCode(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
        ],[ __('auth.email') => __("Email"),]);

        $deviceInfo = $request->input('deviceInfo');
        $email = $request->input('email');

        $code = rand(100000, 999999);
        $expireTime = Carbon::now()->addMinutes(3);

        VerificationMailLog::create([
            'ip' => $deviceInfo['ip'],
            'device_id' => $deviceInfo['device_id'],
            'browser' => $deviceInfo['browser'],
            'os' => $deviceInfo['os'],
            'email' => $email,
            'verification_code' => $code,
            'expires_at' => $expireTime,
        ]);

        Mail::send("emails.verification", ["code" => $code, "email" => $email], function (Message $message) use ($email) {
            $message->to($email);
            $message->subject("dcat驗證信件");
        });

        return response()->json(['message' => '驗證碼已發送']);
    }
}
