<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wishlist;

class WishlistPolicy
{
    /**
     * Determine if the user can view the wishlist.
     */
    public function view(User $user, Wishlist $wishlist): bool
    {
        // Owner can always view, public wishlists can be viewed by anyone
        return $user->id === $wishlist->user_id || $wishlist->is_public;
    }

    /**
     * Determine if the user can update the wishlist.
     */
    public function update(User $user, Wishlist $wishlist): bool
    {
        return $user->id === $wishlist->user_id;
    }

    /**
     * Determine if the user can delete the wishlist.
     */
    public function delete(User $user, Wishlist $wishlist): bool
    {
        return $user->id === $wishlist->user_id;
    }
}
