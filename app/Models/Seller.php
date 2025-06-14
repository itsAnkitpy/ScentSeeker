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
    ];

    /**
     * Get the prices for the seller.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }
}
