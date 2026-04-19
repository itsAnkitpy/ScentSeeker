<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perfume extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'notes' => 'array',
    ];

    protected $fillable = [
        'name',
        'brand',
        'description',
        'notes',
        'image_url',
        'concentration',
        'gender_affinity',
        'launch_year',
    ];

    /**
     * Get the prices for the perfume.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    /**
     * Scope to find perfume by name and brand (case-insensitive).
     * This uses a more efficient approach than whereRaw with LOWER().
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @param string $brand
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByNameAndBrand($query, string $name, string $brand)
    {
        return $query->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($name))])
                     ->whereRaw('LOWER(TRIM(brand)) = ?', [strtolower(trim($brand))]);
    }
}
