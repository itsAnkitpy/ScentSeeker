<?php

namespace Database\Factories;

use App\Models\Perfume;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceAlert>
 */
class PriceAlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $targetPrice = fake()->randomFloat(2, 1000, 10000);

        return [
            'user_id' => User::factory(),
            'perfume_id' => Perfume::factory(),
            'size_ml' => fake()->randomElement([null, 30, 50, 100]),
            'target_price' => $targetPrice,
            'current_lowest_price' => $targetPrice + fake()->randomFloat(2, 500, 3000),
            'is_active' => true,
            'triggered_at' => null,
            'notification_sent_at' => null,
        ];
    }

    /**
     * Indicate that the alert has been triggered.
     */
    public function triggered(): static
    {
        return $this->state(fn(array $attributes) => [
            'triggered_at' => now(),
            'notification_sent_at' => now(),
        ]);
    }

    /**
     * Indicate that the alert is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the alert is for a specific size.
     */
    public function forSize(int $size): static
    {
        return $this->state(fn(array $attributes) => [
            'size_ml' => $size,
        ]);
    }
}
