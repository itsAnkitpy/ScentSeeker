<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePerfumeRequest;
use App\Http\Requests\UpdatePerfumeRequest;
use App\Http\Resources\PerfumeResource;
use App\Http\Resources\PriceResource;
use App\Models\Perfume;
use App\Services\PerfumeFilterService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class PerfumeController extends Controller
{
    public function __construct(
        protected PerfumeFilterService $filterService,
    ) {}

    /**
     * Display a listing of the resource with advanced filtering.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $version = Cache::get('perfumes.index.version', 1);
        $cacheKey = 'perfumes.index.v' . $version . '.' . md5(serialize($request->query()));

        return Cache::remember($cacheKey, 60, function () use ($request) {
            $query = $this->filterService->apply($request);
            $perfumes = $query->paginate(15);

            return PerfumeResource::collection($perfumes->withQueryString());
        });
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
        $perPage = min((int) request()->input('per_page', 10), 100);
        $page = request()->input('page', 1);
        $cacheKey = 'perfumes.prices.' . $perfume->id . '.pp.' . $perPage . '.page.' . $page;

        $prices = Cache::remember($cacheKey, 1800, function () use ($perfume, $perPage) {
            return $perfume->prices()->with('seller')->paginate($perPage);
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
        // Bump version to invalidate all index cache entries at once
        Cache::increment('perfumes.index.version');
        Cache::forget('perfumes.filters');
    }

    /**
     * Get available filter options.
     */
    public function filters(): \Illuminate\Http\JsonResponse
    {
        $filters = Cache::remember('perfumes.filters', 3600, function () {
            return [
                'brands' => Perfume::whereNotNull('brand')
                    ->distinct()
                    ->orderBy('brand')
                    ->pluck('brand')
                    ->values(),
                'concentrations' => Perfume::whereNotNull('concentration')
                    ->distinct()
                    ->orderBy('concentration')
                    ->pluck('concentration')
                    ->values(),
                'genders' => Perfume::whereNotNull('gender_affinity')
                    ->distinct()
                    ->orderBy('gender_affinity')
                    ->pluck('gender_affinity')
                    ->values(),
                'sizes' => \App\Models\Price::whereNotNull('size_ml')
                    ->distinct()
                    ->orderBy('size_ml')
                    ->pluck('size_ml')
                    ->values(),
                'price_range' => [
                    'min' => \App\Models\Price::min('price') ?? 0,
                    'max' => \App\Models\Price::max('price') ?? 100000,
                ],
            ];
        });

        return response()->json(['data' => $filters]);
    }
}
