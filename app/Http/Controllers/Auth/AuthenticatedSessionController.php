<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    // We can add a 'store' method here later if we want to handle web-based login
    // that doesn't use the API, or a 'destroy' method for web-based logout.
    // For now, login is handled by the API and Alpine.js.
}
