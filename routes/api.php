<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\ProductController;

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
Route::get('/categories', [ProductController::class, 'getCategory']);
Route::get('/product-type-options/{productId}', [ProductController::class, 'getProductTypeOptions']);
Route::get('/product-option-inventories/{productId}', [ProductController::class, 'getInventories']);
Route::post('/product-option-inventories', [ProductController::class, 'saveInventory']);

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
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products-types-options', [ProductController::class, 'storeProductTypeOptions']);
    Route::get('/seller-products', [ProductController::class, 'getSellerProducts']);
});