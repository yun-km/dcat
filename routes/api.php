<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:api'); 

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return [
            'result' => 'success',
            'message' => __('Get user profile'),
            'content' => [
                'user' => [
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'avatar' =>  $request->user()->avatar, 
                ],
            ],
        ];
    });
    Route::post('/profile',  [UserController::class, 'updateProfile']);
});