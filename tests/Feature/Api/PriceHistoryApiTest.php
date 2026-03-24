<?php

namespace Tests\Feature\Api;

use App\Models\Perfume;
use App\Models\Price;
use App\Models\PriceHistory;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceHistoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_price_history(): void
    {
        $perfume = Perfume::factory()->create();
        $seller = Seller::factory()->create();
        $price = Price::factory()->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
            'price' => 5000,
        ]);

        PriceHistory::factory()->count(3)->create([
            'price_id' => $price->id,
        ]);

        $response = $this->getJson("/api/v1/prices/{$price->id}/history");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['date', 'price']],
                'price_id',
                'current_price',
                'currency',
            ])
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('price_id', $price->id);
    }

    public function test_price_history_returns_data_sorted_by_date(): void
    {
        $price = Price::factory()->create();

        PriceHistory::factory()->create(['price_id' => $price->id, 'date' => '2026-01-15', 'price' => 5000]);
        PriceHistory::factory()->create(['price_id' => $price->id, 'date' => '2026-01-01', 'price' => 6000]);
        PriceHistory::factory()->create(['price_id' => $price->id, 'date' => '2026-01-30', 'price' => 4500]);

        $response = $this->getJson("/api/v1/prices/{$price->id}/history");

        $response->assertOk();
        $dates = collect($response->json('data'))->pluck('date')->all();
        $this->assertEquals(['2026-01-01', '2026-01-15', '2026-01-30'], $dates);
    }

    public function test_price_history_returns_empty_for_no_history(): void
    {
        $price = Price::factory()->create();

        $response = $this->getJson("/api/v1/prices/{$price->id}/history");

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_price_history_returns_404_for_nonexistent_price(): void
    {
        $response = $this->getJson('/api/v1/prices/99999/history');

        $response->assertNotFound();
    }
}
