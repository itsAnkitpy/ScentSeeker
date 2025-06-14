<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StagingPerfume extends Model
{
    // Note: HasFactory is not added by default by `php artisan make:model`
    // If you need factories later, you can add `use Illuminate\Database\Eloquent\Factories\HasFactory;`
    // and then `use HasFactory;` trait within the class.

    protected $table = 'staging_perfumes';

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
        'confidence_score',
        'imported_at',
        'processed_at',
        'seller_provided_perfume_id',
        'perfume_name_raw',
        'brand_name_raw',
        'concentration_raw',
        'size_raw',
        'gender_raw',
        'description_raw',
        'notes_raw',
        'image_url_raw',
        'seller_product_url_raw',
        'category_raw',
        'sku_raw',
    ];

    protected $casts = [
        'raw_data_payload' => 'array',
        'error_details' => 'array',
        'notes_raw' => 'array',
        'imported_at' => 'datetime',
        'processed_at' => 'datetime',
        'confidence_score' => 'float',
    ];

    /**
     * Get the staging prices associated with this staged perfume.
     * This assumes a StagingPrice model exists and has a 'staged_perfume_identifier'
     * column that would store the ID of this StagingPerfume.
     */
    public function stagingPrices()
    {
        return $this->hasMany(StagingPrice::class, 'staged_perfume_identifier', 'id');
    }
}
