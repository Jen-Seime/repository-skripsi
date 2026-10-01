<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodis = [
            ['kode' => 'TI', 'nama' => 'Teknik Informatika'],
            ['kode' => 'SI', 'nama' => 'Sistem Informasi'],
            ['kode' => 'MI', 'nama' => 'Manajemen Informatika'],
        ];

        foreach ($prodis as $prodi) {
            ProgramStudi::updateOrCreate(
                ['kode' => $prodi['kode']],
                ['nama' => $prodi['nama']]
            );
        }
    }
}
