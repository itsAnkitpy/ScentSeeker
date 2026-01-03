<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'perfume_id',
        'size_ml',
        'target_price',
        'current_lowest_price',
        'is_active',
        'triggered_at',
        'notification_sent_at',
    ];

    protected $casts = [
        'size_ml' => 'integer',
        'target_price' => 'decimal:2',
        'current_lowest_price' => 'decimal:2',
        'is_active' => 'boolean',
        'triggered_at' => 'datetime',
        'notification_sent_at' => 'datetime',
    ];

    /**
     * The user who created this alert.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The perfume being watched.
     */
    public function perfume(): BelongsTo
    {
        return $this->belongsTo(Perfume::class);
    }

    /**
     * Check if the alert should trigger based on current price.
     */
    public function shouldTrigger(float $currentPrice): bool
    {
        return $this->is_active && $currentPrice <= $this->target_price;
    }

    /**
     * Mark the alert as triggered.
     */
    public function markAsTriggered(): void
    {
        $this->update([
            'triggered_at' => now(),
            'notification_sent_at' => now(),
        ]);
    }

    /**
     * Scope to get only active alerts that haven't been triggered.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get alerts ready for checking (not recently notified).
     */
    public function scopeReadyForCheck($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('notification_sent_at')
                    ->orWhere('notification_sent_at', '<', now()->subHours(24));
            });
    }
}
