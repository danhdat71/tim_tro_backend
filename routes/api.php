<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminProductReportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PriceLocaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderMypageController;
use App\Http\Controllers\UserProductController;
use App\Http\Middleware\LimitRequest\LimitBugReportRequest;
use App\Http\Middleware\LimitRequest\LimitLoginMiddleware;
use App\Http\Middleware\LimitRequest\LimitReportMiddleware;
use App\Http\Middleware\LimitRequest\SendOTPMiddleware;
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

Route::post('auth/login', [AuthUserController::class, 'login'])
    ->middleware(LimitLoginMiddleware::class);
Route::post('auth/register', [AuthUserController::class, 'register']);
Route::post('auth/resend-otp', [AuthUserController::class, 'resendOTP'])
    ->middleware(SendOTPMiddleware::class);
Route::post('auth/verify-otp', [AuthUserController::class, 'verifyOTP']);
Route::post('auth/forgot-password', [AuthUserController::class, 'forgotPassword']);
Route::post('auth/verify-otp-change-password', [AuthUserController::class, 'verifyOTPChangePassword']);
Route::post('auth/change-password', [AuthUserController::class, 'changePassword']);

// Provider mypage main info
Route::get('public-provider/{app_id}', [ProviderMypageController::class, 'publicInfo']);
// Provider mypage products
Route::get('public-provider/{app_id}/products', [ProviderMypageController::class, 'publicProducts']);
// Detail hostel
Route::get('public-product/{slug}', [ProductController::class, 'publicDetail']);
// Public list products all pages
Route::get('products', [ProductController::class, 'publicList']);
// Public all provinces
Route::get('provinces', [LocationController::class, 'publicProvinces']);
Route::get('provinces-with-districts', [LocationController::class, 'publicProvincesWithDistricts']);
// Search other hostel with price less than current hostel viewing
Route::get('wards-with-count-products', [LocationController::class, 'publicWardsWithCountProducts']);
// Get district with product count - Detail product page
Route::get('districts-with-count-products', [LocationController::class, 'publicDistrictWithCountProducts']);
Route::post('user/report-product', [UserProductController::class, 'reportProduct'])
    ->middleware(LimitReportMiddleware::class);
// Get prices with count product
Route::get('prices-with-count', [ProductController::class, 'getPriceWithProductCount']);

// Get location
Route::get('location/get-provinces', [LocationController::class, 'getProvinces']);
Route::get('location/get-districts', [LocationController::class, 'getDistricts']);
Route::get('location/get-wards', [LocationController::class, 'getWards']);

// Bug report
Route::post('bug-report/store', [BugReportController::class, 'store'])
    ->middleware(LimitBugReportRequest::class);

// Locale price calculate
Route::get('price-locale', [PriceLocaleController::class, 'index']);

Route::group([
    'prefix' => '',
    'middleware' => ['auth:sanctum', 'check-active'],
], function(){
    Route::get('/auth/get-me', [AuthUserController::class, 'getMe']);
    Route::post('/auth/logout', [AuthUserController::class, 'logout']);

    // Provider mypage info
    Route::get('provider/mypage', [ProviderMypageController::class, 'mypage']);
    Route::post('provider/update-avatar', [ProviderMypageController::class, 'updateAvatar']);
    Route::post('provider/update-item-info', [ProviderMypageController::class, 'updateItemData']);
    // Provider mypage follower
    Route::get('provider/followings', [FollowController::class, 'followers']);
    // Provider product manager
    Route::post('provider/product/store', [ProductController::class, 'store']);
    Route::post('provider/product/store-draft', [ProductController::class, 'storeDraft']);
    Route::post('provider/product/update', [ProductController::class, 'update']);
    Route::get('provider/product/list', [ProductController::class, 'listByAuth']);
    Route::post('provider/product/delete', [ProductController::class, 'delete']);
    Route::get('provider/product/detail', [ProductController::class, 'detail']);
    Route::post('provider/product/public-draft', [ProductController::class, 'publicDraft']);

    // Finder
    Route::post('finder/follow', [FollowController::class, 'follow']);
    Route::get('finder/followings', [FollowController::class, 'following']);
    Route::get('finder/viewed-list', [UserProductController::class, 'listViewedProduct']);

    Route::post('user/save-product', [UserProductController::class, 'saveProduct']);
    Route::get('user/list-saved-products', [UserProductController::class, 'listSavedProducts']);

    Route::post('user/leave', [AuthUserController::class, 'leaveSystem']);

    // Notification list
    Route::get('notifications', [NotificationController::class, 'getList']);
    Route::post('notification/mark-read', [NotificationController::class, 'markRead']);
    Route::post('notification/mark-read-all', [NotificationController::class, 'markReadAll']);

    Route::group([
        'middleware' => 'role-admin',
        'prefix' => '/admin',
    ], function(){
        // Bug report
        Route::get('bug-report/list', [BugReportController::class, 'getList']);
        Route::get('bug-report/{id}', [BugReportController::class, 'getDetail']);
        Route::post('bug-report/status', [BugReportController::class, 'status']);

        // Products
        Route::get('products/list', [AdminProductController::class, 'getList']);
        Route::get('products/{id}', [AdminProductController::class, 'getDetail']);
        Route::post('products/status', [AdminProductController::class, 'status']);

        // Product report
        Route::get('product-report/list', [AdminProductReportController::class, 'getList']);
        Route::get('product-report/{id}', [AdminProductReportController::class, 'getDetailProductReport']);
        Route::get('product-report/{id}/list', [AdminProductReportController::class, 'getListReport']);
        Route::get('product-report/{id}/detail', [AdminProductReportController::class, 'getDetailReport']);
        Route::post('product-report/status', [AdminProductReportController::class, 'status']);

        // Dashboard
        Route::get('dashboard/index', [AdminDashboardController::class, 'index']);

        // Product policy report
        Route::get('users/index', [AdminUserController::class, 'index']);
        Route::get('users/{id}', [AdminUserController::class, 'detailUser']);
        Route::post('users/status', [AdminUserController::class, 'updateStatus']);
    });
});
