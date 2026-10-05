<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkripsiResource extends JsonResource
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
            'judul' => $this->judul,
            'abstrak' => $this->abstrak,
            'kata_kunci' => $this->kata_kunci,
            'tahun' => $this->tahun,
            'status' => $this->status,
            'di_upload' => $this->created_at,

            'program_studi' => $this->whenLoaded('programStudi', function () {
                return [
                    'kode' => $this->programStudi?->kode,
                    'nama' => $this->programStudi?->nama,
                ];
            }),

            'kategori' => $this->whenLoaded('kategori', function () {
                return [
                    'nama' => $this->kategori?->nama,
                ];
            }),

            'mahasiswa' => $this->whenLoaded('mahasiswa', function () {
                return [
                    'id' => $this->mahasiswa?->id,
                    'nama' => $this->mahasiswa?->user?->name,
                    'nim' => $this->mahasiswa?->nim,
                ];
            }),

            'files' => $this->whenLoaded('files', function () {
                return $this->files->map(fn ($file) => [
                    'id' => $file->id,
                    'jenis' => $file->jenis,
                    'nama_file' => $file->nama_file,
                    'path' => $file->path,
                    'ukuran' => $file->ukuran,
                ]);
            }),
        ];
    }
}
