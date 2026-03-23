<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PasswordResetController;
use App\Http\Controllers\Api\V1\PerfumeController;
use App\Http\Controllers\Api\V1\PriceAlertController;
use App\Http\Controllers\Api\V1\PriceController;
use App\Http\Controllers\Api\V1\WishlistController;
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

Route::group(['prefix' => 'v1', 'as' => 'api.v1.', 'middleware' => 'throttle:60,1'], function () {
    // Authentication routes (stricter limit for auth)
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    // Password reset routes (public, with strict throttling)
    Route::middleware('throttle:6,1')->group(function () {
        Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
        Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
    });

    // Public perfume routes (read-only)
    Route::get('perfumes/filters', [PerfumeController::class, 'filters'])->name('perfumes.filters');
    Route::get('perfumes/{perfume}/prices', [PerfumeController::class, 'prices'])->name('perfumes.prices');
    Route::apiResource('perfumes', PerfumeController::class)->only(['index', 'show']);

    // Price history route (public)
    Route::get('prices/{price}/history', [PriceController::class, 'history'])->name('prices.history');

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        // Logout endpoints
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');

        // Email verification resend
        Route::post('/email/resend', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();
            return response()->json(['message' => 'Verification email sent']);
        })->name('verification.send');

        // Protected perfume CRUD (admin only)
        Route::middleware('admin')->group(function () {
            Route::apiResource('perfumes', PerfumeController::class)->only(['store', 'update', 'destroy']);
        });

        // Wishlist routes
        Route::prefix('wishlists')->name('wishlists.')->group(function () {
            Route::get('/', [WishlistController::class, 'index'])->name('index');
            Route::post('/', [WishlistController::class, 'store'])->name('store');
            Route::get('/{wishlist}', [WishlistController::class, 'show'])->name('show');
            Route::put('/{wishlist}', [WishlistController::class, 'update'])->name('update');
            Route::delete('/{wishlist}', [WishlistController::class, 'destroy'])->name('destroy');
            Route::post('/{wishlist}/items', [WishlistController::class, 'addItem'])->name('items.add');
            Route::delete('/{wishlist}/items/{perfume}', [WishlistController::class, 'removeItem'])->name('items.remove');
        });

        // Wishlist quick actions (for heart icon toggle)
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::get('/wishlist/check', [WishlistController::class, 'check'])->name('wishlist.check');

        // Price alert routes
        Route::prefix('price-alerts')->name('price-alerts.')->group(function () {
            Route::get('/', [PriceAlertController::class, 'index'])->name('index');
            Route::post('/', [PriceAlertController::class, 'store'])->name('store');
            Route::get('/check', [PriceAlertController::class, 'check'])->name('check');
            Route::get('/{priceAlert}', [PriceAlertController::class, 'show'])->name('show');
            Route::put('/{priceAlert}', [PriceAlertController::class, 'update'])->name('update');
            Route::delete('/{priceAlert}', [PriceAlertController::class, 'destroy'])->name('destroy');
        });
    });
});