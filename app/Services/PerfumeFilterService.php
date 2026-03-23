<?php

namespace App\Services;

use App\Models\Perfume;
use App\Models\Price;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PerfumeFilterService
{
    /**
     * Apply all filters from the request to a perfume query.
     */
    public function apply(Request $request): Builder
    {
        $query = Perfume::query()->select('perfumes.*');

        $this->applySearch($query, $request->input('search'));
        $this->applyListFilter($query, 'brand', $request->input('brands'));
        $this->applyListFilter($query, 'concentration', $request->input('concentrations'));
        $this->applyListFilter($query, 'gender_affinity', $request->input('genders'));
        $this->applyNotesFilter($query, $request->input('notes'));
        $this->applyPriceRange($query, $request->input('min_price'), $request->input('max_price'));
        $this->applySizeFilter($query, $request->input('sizes'));
        $this->addPriceStats($query);
        $this->applySorting($query, $request->input('sort', 'name_asc'));

        return $query;
    }

    protected function applySearch(Builder $query, ?string $search): void
    {
        if (! $search) {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('brand', 'like', '%' . $search . '%');
        });
    }

    protected function applyListFilter(Builder $query, string $column, mixed $values): void
    {
        if (! $values) {
            return;
        }

        $list = is_array($values) ? $values : explode(',', $values);
        $query->whereIn($column, $list);
    }

    protected function applyNotesFilter(Builder $query, mixed $notes): void
    {
        if (! $notes) {
            return;
        }

        $noteList = is_array($notes) ? $notes : explode(',', $notes);
        $query->where(function ($q) use ($noteList) {
            foreach ($noteList as $note) {
                $q->orWhereJsonContains('notes', $note);
            }
        });
    }

    protected function applyPriceRange(Builder $query, mixed $minPrice, mixed $maxPrice): void
    {
        if (! ($minPrice > 0 || $maxPrice)) {
            return;
        }

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

    protected function applySizeFilter(Builder $query, mixed $sizes): void
    {
        if (! $sizes) {
            return;
        }

        $sizeList = is_array($sizes) ? $sizes : explode(',', $sizes);
        $query->whereHas('prices', function ($q) use ($sizeList) {
            $q->whereIn('size_ml', $sizeList);
        });
    }

    protected function addPriceStats(Builder $query): void
    {
        $query->addSelect([
            'min_price' => Price::selectRaw('MIN(price)')
                ->whereColumn('perfume_id', 'perfumes.id')
                ->where('stock_status', 'In Stock'),
            'seller_count' => Price::selectRaw('COUNT(DISTINCT seller_id)')
                ->whereColumn('perfume_id', 'perfumes.id')
                ->where('stock_status', 'In Stock'),
        ]);
    }

    protected function applySorting(Builder $query, string $sortBy): void
    {
        match ($sortBy) {
            'price_low_to_high' => $query->orderBy('min_price', 'asc'),
            'price_high_to_low' => $query->orderBy('min_price', 'desc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'brand_asc' => $query->orderBy('brand', 'asc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('name', 'asc'),
        };
    }
}
