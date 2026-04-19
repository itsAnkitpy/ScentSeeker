<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'seller_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller' && $this->seller_id !== null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->is_admin === true,
            'seller' => $this->isSeller(),
            default => false,
        };
    }

    /**
     * Get the name to display in Filament (uses username since we don't have a name column).
     */
    public function getFilamentName(): string
    {
        return $this->username ?? $this->email;
    }

    /**
     * Accessor for 'name' attribute - Filament expects this.
     * Returns username since we don't have a name column.
     */
    public function getNameAttribute(): string
    {
        return $this->username ?? $this->email ?? '';
    }

    /**
     * Get the user's wishlists.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the user's price alerts.
     */
    public function priceAlerts()
    {
        return $this->hasMany(PriceAlert::class);
    }

    /**
     * Get the user's default wishlist, creating one if it doesn't exist.
     */
    public function getDefaultWishlist(): Wishlist
    {
        return $this->wishlists()->firstOrCreate(
            ['name' => 'My Wishlist'],
            ['is_public' => false]
        );
    }
}
