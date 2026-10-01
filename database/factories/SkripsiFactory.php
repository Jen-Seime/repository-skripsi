<?php

namespace Database\Factories;

use App\Enums\SkripsiStatus;
use App\Models\Kategori;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Skripsi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skripsi>
 */
class SkripsiFactory extends Factory
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
            'program_studi_id' => ProgramStudi::factory(),
            'kategori_id' => Kategori::factory(),
            'judul' => fake()->sentence(6),
            'abstrak' => fake()->paragraphs(3, true),
            'kata_kunci' => implode(', ', fake()->words(4)),
            'tahun' => fake()->numberBetween(2020, 2026),
            'status' => SkripsiStatus::Menunggu,
        ];
    }

    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SkripsiStatus::Disetujui,
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SkripsiStatus::Ditolak,
        ]);
    }
}
