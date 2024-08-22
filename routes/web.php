<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/home', function () {
//     return view('home');
// });

Route::get('/auth/line', [LoginController::class, 'lineLogin']);
Route::get('/auth/line/callback', [LoginController::class, 'lineLoginCallback']);
Route::get('/auth/google', [LoginController::class, 'googleLogin'])->name('/auth/google');
Route::get('/auth/google/callback', [LoginController::class, 'googleLoginCallback'])->name('/auth/google/callback');
require __DIR__.'/auth.php';
