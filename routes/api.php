<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PasswordResetController;
use App\Http\Controllers\Api\V1\PerfumeController;
use App\Http\Controllers\Api\V1\PriceController;
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

        // Protected perfume CRUD (admin operations)
        Route::apiResource('perfumes', PerfumeController::class)->only(['store', 'update', 'destroy']);
    });
});