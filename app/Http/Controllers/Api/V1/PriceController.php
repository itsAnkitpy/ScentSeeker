<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Models\PriceHistory;
use Illuminate\Http\JsonResponse;

class PriceController extends Controller
{
    /**
     * Get price history for a specific price record.
     * Returns data formatted for Chart.js consumption.
     */
    public function history(Price $price): JsonResponse
    {
        $history = PriceHistory::where('price_id', $price->id)
            ->orderBy('date', 'asc')
            ->select('date', 'price')
            ->get();

        return response()->json([
            'data' => $history,
            'price_id' => $price->id,
            'current_price' => $price->price,
            'currency' => $price->currency,
        ]);
    }
}
