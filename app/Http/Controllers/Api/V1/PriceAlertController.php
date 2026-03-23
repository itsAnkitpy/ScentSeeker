<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PriceAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceAlertController extends Controller
{
    /**
     * List all price alerts for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $alerts = $request->user()
            ->priceAlerts()
            ->with('perfume')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $alerts,
        ]);
    }

    /**
     * Create a new price alert.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'perfume_id' => 'required|exists:perfumes,id',
            'size_ml' => 'nullable|integer|min:1',
            'target_price' => 'required|numeric|min:1',
        ]);

        // Check if user already has an alert for this perfume+size combo
        $sizeml = $validated['size_ml'] ?? null;
        $existingAlert = $request->user()->priceAlerts()
            ->where('perfume_id', $validated['perfume_id'])
            ->when($sizeml !== null,
                fn ($q) => $q->where('size_ml', $sizeml),
                fn ($q) => $q->whereNull('size_ml'),
            )
            ->first();

        if ($existingAlert) {
            return response()->json([
                'message' => 'You already have an alert for this perfume' . (isset($validated['size_ml']) ? ' and size' : ''),
                'data' => $existingAlert,
            ], 422);
        }

        // Get current lowest price for this perfume (filtered by size if specified)
        $priceQuery = \App\Models\Price::where('perfume_id', $validated['perfume_id']);
        if (isset($validated['size_ml'])) {
            $priceQuery->where('size_ml', $validated['size_ml']);
        }
        $currentLowest = $priceQuery->min('price');

        $alert = $request->user()->priceAlerts()->create([
            'perfume_id' => $validated['perfume_id'],
            'size_ml' => $validated['size_ml'] ?? null,
            'target_price' => $validated['target_price'],
            'current_lowest_price' => $currentLowest,
            'is_active' => true,
        ]);

        $alert->load('perfume');

        return response()->json([
            'data' => $alert,
            'message' => 'Price alert created successfully',
        ], 201);
    }

    /**
     * Show a specific price alert.
     */
    public function show(PriceAlert $priceAlert): JsonResponse
    {
        $this->authorize('view', $priceAlert);

        $priceAlert->load('perfume');

        return response()->json([
            'data' => $priceAlert,
        ]);
    }

    /**
     * Update a price alert (primarily target_price).
     */
    public function update(Request $request, PriceAlert $priceAlert): JsonResponse
    {
        $this->authorize('update', $priceAlert);

        $validated = $request->validate([
            'target_price' => 'sometimes|numeric|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        // If reactivating, clear triggered status
        if (isset($validated['is_active']) && $validated['is_active']) {
            $validated['triggered_at'] = null;
            $validated['notification_sent_at'] = null;
        }

        $priceAlert->update($validated);

        return response()->json([
            'data' => $priceAlert,
            'message' => 'Price alert updated successfully',
        ]);
    }

    /**
     * Delete a price alert.
     */
    public function destroy(PriceAlert $priceAlert): JsonResponse
    {
        $this->authorize('delete', $priceAlert);

        $priceAlert->delete();

        return response()->json([
            'message' => 'Price alert deleted successfully',
        ]);
    }

    /**
     * Check if user has an alert for a specific perfume.
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'perfume_id' => 'required|exists:perfumes,id',
        ]);

        $alert = $request->user()
            ->priceAlerts()
            ->where('perfume_id', $validated['perfume_id'])
            ->first();

        return response()->json([
            'has_alert' => $alert !== null,
            'alert' => $alert,
        ]);
    }
}
