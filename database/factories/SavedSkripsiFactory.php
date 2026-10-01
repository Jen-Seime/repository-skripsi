<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use App\Models\SavedSkripsi;
use App\Models\Skripsi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavedSkripsi>
 */
class SavedSkripsiFactory extends Factory
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
            'skripsi_id' => Skripsi::factory(),
            'created_at' => now(),
        ];
    }
}
