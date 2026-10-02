<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'mahasiswa' => $this->whenLoaded('mahasiswa', function () {
                if (! $this->mahasiswa) {
                    return null;
                }

                return [
                    'nim' => $this->mahasiswa->nim,
                    'angkatan' => $this->mahasiswa->angkatan,
                    'program_studi' => $this->mahasiswa->programStudi?->nama,
                ];
            }),
        ];
    }
}
