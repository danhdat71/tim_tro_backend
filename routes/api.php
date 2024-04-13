<?php

use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderMypageController;
use App\Http\Controllers\UploadImageController;
use App\Http\Middleware\LimitRequest\LimitLoginMiddleware;
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

Route::post('auth/login', [AuthUserController::class, 'login']);
    #->middleware(LimitLoginMiddleware::class);
Route::post('auth/register', [AuthUserController::class, 'register']);
Route::post('auth/resend-otp', [AuthUserController::class, 'resendOTP'])
    ->middleware(SendOTPMiddleware::class);
Route::post('auth/verify-otp', [AuthUserController::class, 'verifyOTP']);
Route::post('auth/forgot-password', [AuthUserController::class, 'forgotPassword']);
Route::post('auth/verify-otp-change-password', [AuthUserController::class, 'verifyOTPChangePassword']);
Route::post('auth/change-password', [AuthUserController::class, 'changePassword']);

Route::group([
    'prefix' => '',
    'middleware' => 'auth:sanctum',
], function(){
    Route::get('/auth/get-me', [AuthUserController::class, 'getMe']);
    Route::post('/auth/logout', [AuthUserController::class, 'logout']);

    Route::get('provider/mypage', [ProviderMypageController::class, 'mypage']);
    Route::post('provider/update-avatar', [ProviderMypageController::class, 'updateAvatar']);
    Route::post('provider/update-item-info', [ProviderMypageController::class, 'updateItemData']);

    Route::post('provider/product/store', [ProductController::class, 'store']);
    Route::post('provider/product/update', [ProductController::class, 'update']);
    Route::get('provider/product/list', [ProductController::class, 'list']);
    Route::post('provider/product/delete', [ProductController::class, 'delete']);
});
