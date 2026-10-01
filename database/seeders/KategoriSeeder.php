<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriList = [
            'Artificial Intelligence',
            'Internet of Things',
            'Sistem Informasi',
            'Web Development',
            'Mobile Development',
            'Data Mining',
            'UI/UX',
            'Jaringan Komputer',
        ];

        foreach ($kategoriList as $nama) {
            Kategori::updateOrCreate(
                ['slug' => Str::slug($nama)],
                ['nama' => $nama]
            );
        }
    }
}
