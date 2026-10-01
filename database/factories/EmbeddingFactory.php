<?php

namespace Database\Factories;

use App\Models\Embedding;
use App\Models\Skripsi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Embedding>
 */
class EmbeddingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'skripsi_id' => Skripsi::factory(),
            'model' => 'text-embedding-3-small',
            'dimension' => 1536,
            'embedding' => array_map(fn () => fake()->randomFloat(6, -1, 1), range(1, 10)),
        ];
    }
}
