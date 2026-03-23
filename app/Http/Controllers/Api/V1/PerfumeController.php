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
     * Display a listing of the resource with advanced filtering.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Perfume::query()->select('perfumes.*');

        // Text search (name or brand)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%');
            });
        }

        // Brand filter (multi-select)
        if ($brands = $request->input('brands')) {
            $brandList = is_array($brands) ? $brands : explode(',', $brands);
            $query->whereIn('brand', $brandList);
        }

        // Concentration filter (EDP, EDT, Parfum, etc.)
        if ($concentrations = $request->input('concentrations')) {
            $concList = is_array($concentrations) ? $concentrations : explode(',', $concentrations);
            $query->whereIn('concentration', $concList);
        }

        // Gender filter
        if ($genders = $request->input('genders')) {
            $genderList = is_array($genders) ? $genders : explode(',', $genders);
            $query->whereIn('gender_affinity', $genderList);
        }

        // Notes filter (JSON array contains any of the selected notes)
        if ($notes = $request->input('notes')) {
            $noteList = is_array($notes) ? $notes : explode(',', $notes);
            $query->where(function ($q) use ($noteList) {
                foreach ($noteList as $note) {
                    $q->orWhereJsonContains('notes', $note);
                }
            });
        }

        // Price range filter — single whereHas so min/max apply to the SAME price row
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        if ($minPrice > 0 || $maxPrice) {
            $query->whereHas('prices', function ($q) use ($minPrice, $maxPrice) {
                $q->where('stock_status', 'In Stock');
                if ($minPrice > 0) {
                    $q->where('price', '>=', $minPrice);
                }
                if ($maxPrice) {
                    $q->where('price', '<=', $maxPrice);
                }
            });
        }

        // Size filter (filter perfumes that have prices with specific sizes)
        if ($sizes = $request->input('sizes')) {
            $sizeList = is_array($sizes) ? $sizes : explode(',', $sizes);
            $query->whereHas('prices', function ($q) use ($sizeList) {
                $q->whereIn('size_ml', $sizeList);
            });
        }

        // Always include price stats via subqueries (no join conflicts)
        $query->addSelect([
            'min_price' => \App\Models\Price::selectRaw('MIN(price)')
                ->whereColumn('perfume_id', 'perfumes.id')
                ->where('stock_status', 'In Stock'),
            'seller_count' => \App\Models\Price::selectRaw('COUNT(DISTINCT seller_id)')
                ->whereColumn('perfume_id', 'perfumes.id')
                ->where('stock_status', 'In Stock'),
        ]);

        // Sorting
        $sortBy = $request->input('sort', 'name_asc');
        switch ($sortBy) {
            case 'price_low_to_high':
                $query->orderBy('min_price', 'asc');
                break;
            case 'price_high_to_low':
                $query->orderBy('min_price', 'desc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'brand_asc':
                $query->orderBy('brand', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default: // name_asc
                $query->orderBy('name', 'asc');
        }

        $perfumes = $query->paginate(15);

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
