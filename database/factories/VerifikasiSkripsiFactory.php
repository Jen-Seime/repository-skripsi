<?php

namespace Database\Factories;

use App\Enums\VerifikasiStatus;
use App\Models\Skripsi;
use App\Models\User;
use App\Models\VerifikasiSkripsi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VerifikasiSkripsi>
 */
class VerifikasiSkripsiFactory extends Factory
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
            'diverifikasi_oleh' => User::factory()->kepalaPerpustakaan(),
            'status' => VerifikasiStatus::Disetujui,
            'catatan' => fake()->sentence(),
            'verified_at' => now(),
        ];
    }

    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VerifikasiStatus::Disetujui,
            'catatan' => 'Berkas dan format skripsi telah memenuhi standar.',
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VerifikasiStatus::Ditolak,
            'catatan' => 'Format daftar pustaka tidak sesuai dengan panduan skripsi.',
        ]);
    }
}
