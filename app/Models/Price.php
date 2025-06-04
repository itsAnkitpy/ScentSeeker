<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'perfume_id',
        'seller_id',
        'price',
        'currency',
        'stock_status',
        'product_url',
        'last_updated',
        'offer_details',
        'size_ml',
        'item_type',
    ];

    /**
     * Get the perfume that owns the price.
     */
    public function perfume(): BelongsTo
    {
        return $this->belongsTo(Perfume::class);
    }

    /**
     * Get the seller that owns the price.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Get the price history for the price.
     */
    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class);
    }
}
