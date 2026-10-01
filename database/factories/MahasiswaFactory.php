<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->mahasiswa(),
            'nim' => (string) fake()->unique()->numerify('##########'),
            'program_studi_id' => ProgramStudi::factory(),
            'angkatan' => fake()->numberBetween(2019, 2025),
        ];
    }
}
