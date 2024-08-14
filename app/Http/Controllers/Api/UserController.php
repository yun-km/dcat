<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        \Log::info('Request Data: ' . json_encode($request->all()));
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'result' => 'error',
                    'message' => 'User not authenticated',
                ], 401);
            }
            $user->name = $request->name;
    
            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    \Log::info('Attempting to delete old avatar: ' . $user->avatar);
                    if (Storage::disk('avatars')->exists($user->avatar)) {
                        Storage::disk('avatars')->delete($user->avatar);
                        \Log::info('Old avatar deleted successfully');
                    } else {
                        \Log::info('Old avatar not found');
                    }
                }
                $path = $request->file('avatar')->store('images', 'avatars');
                $user->avatar = $path;
            }
    
            $user->save();
    
            return [
                'result' => 'success',
                'message' => __('Update profile success'),
                'content' => [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar, 
                    ],
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('Profile update error:', ['error' => $e->getMessage()]);
            return response()->json([
                'result' => 'error',
                'message' => 'An error occurred during profile update',
            ], 500);
        }
    }
    
}