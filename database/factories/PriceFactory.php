<?php

namespace Database\Factories;

use App\Models\Perfume;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Price>
 */
class PriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stockStatuses = ['in_stock', 'out_of_stock', 'preorder', 'limited'];
        $itemTypes = ['full_bottle', 'decant', 'sample', 'travel_size'];
        $currencies = ['USD', 'EUR', 'GBP', 'INR'];

        return [
            'perfume_id' => Perfume::factory(),
            'seller_id' => Seller::factory(),
            'price' => fake()->randomFloat(2, 20, 500),
            'currency' => fake()->randomElement($currencies),
            'stock_status' => fake()->randomElement($stockStatuses),
            'product_url' => fake()->url(),
            'last_updated' => fake()->dateTimeThisMonth(),
            'offer_details' => fake()->optional()->sentence(),
            'size_ml' => fake()->randomElement([30, 50, 75, 100, 125, 200]),
            'item_type' => fake()->randomElement($itemTypes),
        ];
    }
}
