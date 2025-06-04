<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'currency' => $this->currency,
            'stock_status' => $this->stock_status,
            'product_url' => $this->product_url,
            'last_updated' => $this->last_updated ? $this->last_updated->toIso8601String() : null,
            'offer_details' => $this->offer_details,
            'size_ml' => $this->size_ml,
            'item_type' => $this->item_type,
            'seller' => [
                'id' => $this->seller->id,
                'name' => $this->seller->name,
                'logo_url' => $this->seller->logo_url,
                'website_url' => $this->seller->website_url,
                'rating' => $this->seller->rating,
                'type' => $this->seller->type,
            ],
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
