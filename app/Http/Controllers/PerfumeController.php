<?php

namespace App\Http\Controllers;

use App\Models\Perfume; // Added for route model binding
use Illuminate\Http\Request;
use Illuminate\View\View; // Added for type hinting

class PerfumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Data fetching is handled by Alpine.js in the Blade view
        return view('perfumes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Perfume $perfume): View
    {
        // The $perfume model instance is automatically resolved by Laravel's route model binding.
        // We pass it to the view, and Alpine.js can use it or fetch more details if needed.
        return view('perfumes.show', compact('perfume'));
    }
}
