<?php

namespace Database\Factories;

use App\Models\Fund;
use App\Models\FundManager;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Fund>
 */
class FundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=> fake()->text,
            'aliases' => fake()->words(),
            'year' => Carbon::now()->year,
            'fund_manager_id' => FundManager::factory()
        ];
    }
}
