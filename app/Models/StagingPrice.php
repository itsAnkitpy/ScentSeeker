<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StagingPrice extends Model
{
    // Note: HasFactory is not added by default by `php artisan make:model`

    protected $table = 'staging_prices';

    protected $fillable = [
        'import_batch_id',
        'source_identifier',
        'seller_code_raw',
        'raw_data_payload',
        'validation_status',
        'processing_status',
        'error_details',
        'is_duplicate_of_staged_id',
        'matched_production_perfume_id',
        'matched_production_price_id',
        'confidence_score',
        'imported_at',
        'processed_at',
        'staged_perfume_identifier', // This will link to StagingPerfume's ID or a unique key
        'price_raw',
        'currency_raw',
        'discount_price_raw',
        'availability_raw',
        'seller_specific_price_id', // If the seller uses their own ID for the price entry
    ];

    protected $casts = [
        'raw_data_payload' => 'array',
        'error_details' => 'array',
        'imported_at' => 'datetime',
        'processed_at' => 'datetime',
        'confidence_score' => 'float',
        'price_raw' => 'decimal:2',
        'discount_price_raw' => 'decimal:2',
    ];

    /**
     * Get the staged perfume record that this price belongs to.
     */
    public function stagingPerfume()
    {
        return $this->belongsTo(StagingPerfume::class, 'staged_perfume_identifier', 'id'); // Assuming 'id' of staging_perfumes
    }
}
