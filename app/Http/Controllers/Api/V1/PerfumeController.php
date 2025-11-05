<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePerfumeRequest;
use App\Http\Requests\UpdatePerfumeRequest;
use App\Http\Resources\PerfumeResource;
use App\Http\Resources\PriceResource;
use App\Models\Perfume;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class PerfumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $searchTerm = $request->input('search');
        $page = $request->input('page', 1);
        
        // Create cache key based on search and page
        $cacheKey = 'perfumes.index.' . md5($searchTerm ?? 'all') . '.page.' . $page;
        
        $perfumes = Cache::remember($cacheKey, 3600, function () use ($searchTerm) {
            $query = Perfume::query();

            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('brand', 'like', '%' . $searchTerm . '%');
                });
            }

            return $query->paginate(15);
        });

        return PerfumeResource::collection($perfumes->withQueryString());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePerfumeRequest $request): PerfumeResource
    {
        $perfume = Perfume::create($request->validated());
        
        // Clear relevant cache (simplified - in production, consider cache tags)
        $this->clearPerfumeCache();
        
        return new PerfumeResource($perfume);
    }

    /**
     * Display the specified resource.
     */
    public function show(Perfume $perfume): PerfumeResource
    {
        $cacheKey = 'perfumes.show.' . $perfume->id;
        
        $perfume = Cache::remember($cacheKey, 3600, function () use ($perfume) {
            return $perfume->load(['prices.seller']);
        });

        return new PerfumeResource($perfume);
    }

    /**
     * Display a listing of prices for the specified perfume.
     */
    public function prices(Perfume $perfume): AnonymousResourceCollection
    {
        $page = request()->input('page', 1);
        $cacheKey = 'perfumes.prices.' . $perfume->id . '.page.' . $page;
        
        $prices = Cache::remember($cacheKey, 1800, function () use ($perfume) {
            return $perfume->prices()->with('seller')->paginate(10);
        });

        return PriceResource::collection($prices);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePerfumeRequest $request, Perfume $perfume): PerfumeResource
    {
        $perfume->update($request->validated());
        
        // Clear relevant cache
        Cache::forget('perfumes.show.' . $perfume->id);
        Cache::forget('perfumes.prices.' . $perfume->id . '.page.1');
        $this->clearPerfumeCache();
        
        return new PerfumeResource($perfume);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perfume $perfume): Response
    {
        // Add authorization check here later (e.g., if (auth()->user()->cannot('delete', $perfume)))
        
        // Clear relevant cache before deletion
        Cache::forget('perfumes.show.' . $perfume->id);
        Cache::forget('perfumes.prices.' . $perfume->id . '.page.1');
        $this->clearPerfumeCache();
        
        $perfume->delete();
        return response()->noContent();
    }

    /**
     * Clear perfume listing cache.
     * This is a simplified approach - in production with Redis, consider using cache tags.
     */
    protected function clearPerfumeCache(): void
    {
        // Clear common cache keys (simplified - production should use cache tags)
        Cache::forget('perfumes.index.all.page.1');
        // Note: In production with many search variations, consider implementing cache tags
        // or a more sophisticated cache invalidation strategy
    }
}
