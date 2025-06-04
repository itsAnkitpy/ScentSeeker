<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'price_id',
        'date',
        'price',
    ];

    /**
     * Get the price that owns the price history record.
     */
    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
    }
}
