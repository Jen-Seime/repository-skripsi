<?php

namespace Database\Factories;

use App\Enums\SkripsiFileType;
use App\Models\Skripsi;
use App\Models\SkripsiFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SkripsiFile>
 */
class SkripsiFileFactory extends Factory
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
            'jenis' => fake()->randomElement(SkripsiFileType::cases()),
            'nama_file' => fake()->word().'.pdf',
            'path' => 'skripsi/'.fake()->uuid().'/'.fake()->word().'.pdf',
            'ukuran' => fake()->numberBetween(100_000, 50_000_000),
            'mime_type' => 'application/pdf',
        ];
    }
}
