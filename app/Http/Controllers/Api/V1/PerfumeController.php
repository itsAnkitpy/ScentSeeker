<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePerfumeRequest;
use App\Http\Requests\UpdatePerfumeRequest;
use App\Http\Resources\PerfumeResource;
use App\Http\Resources\PriceResource;
use App\Models\Perfume;
use Illuminate\Http\Request; // Added for request injection
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response; // Reverted to Illuminate\Http\Response

class PerfumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Perfume::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('brand', 'like', '%' . $searchTerm . '%');
            });
        }

        return PerfumeResource::collection($query->paginate(15)->withQueryString());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePerfumeRequest $request): PerfumeResource
    {
        $perfume = Perfume::create($request->validated());
        return new PerfumeResource($perfume);
    }

    /**
     * Display the specified resource.
     */
    public function show(Perfume $perfume): PerfumeResource
    {
        return new PerfumeResource($perfume);
    }

    /**
     * Display a listing of prices for the specified perfume.
     */
    public function prices(Perfume $perfume): AnonymousResourceCollection
    {
        return PriceResource::collection($perfume->prices()->paginate(10));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePerfumeRequest $request, Perfume $perfume): PerfumeResource
    {
        $perfume->update($request->validated());
        return new PerfumeResource($perfume);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perfume $perfume): Response
    {
        // Add authorization check here later (e.g., if (auth()->user()->cannot('delete', $perfume)))
        $perfume->delete();
        return response()->noContent();
    }
}
