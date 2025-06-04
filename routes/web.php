<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PerfumeController; // Added PerfumeController
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::get('/perfumes', [PerfumeController::class, 'index'])->name('perfumes.index');
Route::get('/perfumes/{perfume}', [PerfumeController::class, 'show'])->name('perfumes.show');
