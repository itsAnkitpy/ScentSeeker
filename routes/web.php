<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PerfumeController; // Added PerfumeController
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

// Keep old welcome page accessible for reference (can be removed later)
Route::get('/welcome-old', function () {
    return view('welcome');
});

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::get('/perfumes', [PerfumeController::class, 'index'])->name('perfumes.index');
Route::get('/perfumes/{perfume}', [PerfumeController::class, 'show'])->name('perfumes.show');

// Email verification route (signed URL from verification email)
Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    $user = User::findOrFail($request->route('id'));

    if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        return redirect('/login')->with('error', 'Invalid verification link');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect('/')->with('message', 'Email already verified');
    }

    $user->markEmailAsVerified();

    return redirect('/login')->with('message', 'Email verified successfully! You can now login.');
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

// Password reset form route (link from email points here)
Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

// Authenticated user pages (auth enforced client-side via localStorage token;
// actual data is protected by Sanctum on the API endpoints these views call)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/wishlist', function () {
    return view('user.wishlist');
})->name('wishlist');

Route::get('/alerts', function () {
    return view('user.alerts');
})->name('alerts');
