<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perfume>
 */
class PerfumeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $concentrations = ['EDP', 'EDT', 'Parfum', 'Cologne', 'Eau Fraiche'];
        $genders = ['Male', 'Female', 'Unisex'];

        return [
            'name' => fake()->unique()->words(2, true),
            'brand' => fake()->company(),
            'description' => fake()->paragraph(),
            'notes' => [
                'top' => fake()->words(3),
                'middle' => fake()->words(3),
                'base' => fake()->words(3),
            ],
            'image_url' => fake()->imageUrl(400, 400, 'perfume'),
            'concentration' => fake()->randomElement($concentrations),
            'gender_affinity' => fake()->randomElement($genders),
            'launch_year' => fake()->year(),
        ];
    }
}
