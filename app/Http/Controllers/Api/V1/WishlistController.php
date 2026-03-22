<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WishlistController extends Controller
{
    /**
     * List all wishlists for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $wishlists = $request->user()
            ->wishlists()
            ->with('items.perfume')
            ->withCount('items')
            ->get();

        return response()->json([
            'data' => $wishlists,
        ]);
    }

    /**
     * Create a new wishlist.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('wishlists')->where('user_id', $request->user()->id),
            ],
            'is_public' => 'boolean',
        ]);

        $wishlist = $request->user()->wishlists()->create($validated);

        return response()->json([
            'data' => $wishlist,
            'message' => 'Wishlist created successfully',
        ], 201);
    }

    /**
     * Show a specific wishlist with its items.
     */
    public function show(Wishlist $wishlist): JsonResponse
    {
        $this->authorize('view', $wishlist);

        $wishlist->load('items.perfume');

        return response()->json([
            'data' => $wishlist,
        ]);
    }

    /**
     * Update a wishlist.
     */
    public function update(Request $request, Wishlist $wishlist): JsonResponse
    {
        $this->authorize('update', $wishlist);

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('wishlists')->where('user_id', $request->user()->id)->ignore($wishlist->id),
            ],
            'is_public' => 'sometimes|boolean',
        ]);

        $wishlist->update($validated);

        return response()->json([
            'data' => $wishlist,
            'message' => 'Wishlist updated successfully',
        ]);
    }

    /**
     * Delete a wishlist.
     */
    public function destroy(Wishlist $wishlist): JsonResponse
    {
        $this->authorize('delete', $wishlist);

        $wishlist->delete();

        return response()->json([
            'message' => 'Wishlist deleted successfully',
        ]);
    }

    /**
     * Add a perfume to a wishlist.
     */
    public function addItem(Request $request, Wishlist $wishlist): JsonResponse
    {
        $this->authorize('update', $wishlist);

        $validated = $request->validate([
            'perfume_id' => 'required|exists:perfumes,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if already exists
        $existing = $wishlist->items()->where('perfume_id', $validated['perfume_id'])->first();
        if ($existing) {
            return response()->json([
                'message' => 'Perfume already in wishlist',
                'data' => $existing,
            ], 200);
        }

        $item = $wishlist->items()->create([
            'perfume_id' => $validated['perfume_id'],
            'notes' => $validated['notes'] ?? null,
            'added_at' => now(),
        ]);

        $item->load('perfume');

        return response()->json([
            'data' => $item,
            'message' => 'Added to wishlist',
        ], 201);
    }

    /**
     * Remove a perfume from a wishlist.
     */
    public function removeItem(Wishlist $wishlist, int $perfumeId): JsonResponse
    {
        $this->authorize('update', $wishlist);

        $deleted = $wishlist->items()->where('perfume_id', $perfumeId)->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'Removed from wishlist',
            ]);
        }

        return response()->json([
            'message' => 'Perfume not found in wishlist',
        ], 404);
    }

    /**
     * Quick toggle: Add/remove from default wishlist.
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'perfume_id' => 'required|exists:perfumes,id',
        ]);

        $wishlist = $request->user()->getDefaultWishlist();
        $existing = $wishlist->items()->where('perfume_id', $validated['perfume_id'])->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'in_wishlist' => false,
                'message' => 'Removed from wishlist',
            ]);
        }

        $wishlist->items()->create([
            'perfume_id' => $validated['perfume_id'],
            'added_at' => now(),
        ]);

        return response()->json([
            'in_wishlist' => true,
            'message' => 'Added to wishlist',
        ]);
    }

    /**
     * Check if a perfume is in user's default wishlist.
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'perfume_id' => 'required|exists:perfumes,id',
        ]);

        $wishlist = $request->user()->wishlists()->where('name', 'My Wishlist')->first();

        $inWishlist = $wishlist
            ? $wishlist->items()->where('perfume_id', $validated['perfume_id'])->exists()
            : false;

        return response()->json([
            'in_wishlist' => $inWishlist,
        ]);
    }
}
