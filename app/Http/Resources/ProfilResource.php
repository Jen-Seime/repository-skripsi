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
            'is_active' => (bool) ($this->is_active ?? true),
            'mahasiswa' => $this->whenLoaded('mahasiswa', function () {
                if (! $this->mahasiswa) {
                    return null;
                }

                return [
                    'id' => $this->mahasiswa->id,
                    'nim' => $this->mahasiswa->nim,
                    'angkatan' => $this->mahasiswa->angkatan,
                    'program_studi_id' => $this->mahasiswa->program_studi_id,
                    'program_studi' => $this->mahasiswa->programStudi?->nama,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
