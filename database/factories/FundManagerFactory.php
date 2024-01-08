<?php

namespace Database\Factories;

use App\Models\FundManager;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundManager>
 */
class FundManagerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company()
        ];
    }
}
