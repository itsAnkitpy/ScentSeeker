<?php

namespace App\Policies;

use App\Models\PriceAlert;
use App\Models\User;

class PriceAlertPolicy
{
    /**
     * Determine if the user can view the price alert.
     */
    public function view(User $user, PriceAlert $priceAlert): bool
    {
        return $user->id === $priceAlert->user_id;
    }

    /**
     * Determine if the user can update the price alert.
     */
    public function update(User $user, PriceAlert $priceAlert): bool
    {
        return $user->id === $priceAlert->user_id;
    }

    /**
     * Determine if the user can delete the price alert.
     */
    public function delete(User $user, PriceAlert $priceAlert): bool
    {
        return $user->id === $priceAlert->user_id;
    }
}
