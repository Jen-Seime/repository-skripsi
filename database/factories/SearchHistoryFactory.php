<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use App\Models\SearchHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SearchHistory>
 */
class SearchHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mahasiswa_id' => Mahasiswa::factory(),
            'query' => fake()->sentence(3),
            'created_at' => now(),
        ];
    }
}
