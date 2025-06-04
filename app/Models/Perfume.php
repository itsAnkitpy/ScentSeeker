<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perfume extends Model
{
    use HasFactory;

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
}
