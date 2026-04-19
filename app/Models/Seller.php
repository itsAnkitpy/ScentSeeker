<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'logo_url',
        'website_url',
        'rating',
        'contact_info',
        'type',
        'onboarding_status',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];

    /**
     * Set the rating attribute, clamping to 0.0–5.0 range.
     */
    protected function setRatingAttribute(?float $value): void
    {
        $this->attributes['rating'] = $value !== null
            ? max(0, min(5.0, $value))
            : null;
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
