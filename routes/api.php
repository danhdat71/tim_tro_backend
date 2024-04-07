<?php

use App\Http\Controllers\AuthUserController;
use App\Http\Middleware\LimitRequest\SendOTPMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/login', [AuthUserController::class, 'login'])->name('user.login');
Route::post('auth/register', [AuthUserController::class, 'register'])->name('user.register');
Route::post('auth/resend-otp', [AuthUserController::class, 'resendOTP'])
    ->name('user.register')
    ->middleware(SendOTPMiddleware::class);
Route::post('auth/verify-otp', [AuthUserController::class, 'verifyOTP'])
    ->name('user.verify-otp');

Route::group([
    'prefix' => '',
    'middleware' => 'auth:sanctum',
], function(){
    Route::get('/test', function(){
        echo 123;
    });
});
