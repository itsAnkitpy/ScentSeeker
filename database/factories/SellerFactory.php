<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seller>
 */
class SellerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['official_retailer', 'reddit_seller', 'online_store', 'marketplace'];

        return [
            'name' => fake()->company(),
            'logo_url' => fake()->imageUrl(100, 100, 'logo'),
            'website_url' => fake()->url(),
            'rating' => fake()->randomFloat(1, 1, 5),
            'contact_info' => fake()->email(),
            'type' => fake()->randomElement($types),
        ];
    }
}
