<?php

namespace App\Services;

use App\Models\ProgramStudi;

class ProgramStudiService
{
    public function getProgramStudi()
    {
        return ProgramStudi::all();
    }

    public function createProgramStudi(array $data): ProgramStudi
    {
        return ProgramStudi::create([
            'kode' => $data['kode'],
            'nama' => $data['nama'],
        ]);
    }

    public function updateProgramStudi(array $data, int $id): ProgramStudi
    {
        $programStudi = ProgramStudi::findOrFail($id);
        $programStudi->update([
            'kode' => $data['kode'],
            'nama' => $data['nama'],
        ]);

        return $programStudi;
    }

    public function deleteProgramStudi(int $id): void
    {
        ProgramStudi::findOrFail($id)->delete();
    }
}
