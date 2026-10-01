<?php

namespace App\Models;

use App\Enums\SkripsiStatus;
use Database\Factories\SkripsiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'mahasiswa_id',
    'program_studi_id',
    'kategori_id',
    'judul',
    'abstrak',
    'kata_kunci',
    'tahun',
    'status',
])]
class Skripsi extends Model
{
    /** @use HasFactory<SkripsiFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'skripsi';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'status' => SkripsiStatus::class,
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SkripsiFile::class);
    }

    public function embeddings(): HasMany
    {
        return $this->hasMany(Embedding::class);
    }

    public function latestEmbedding(): HasOne
    {
        return $this->hasOne(Embedding::class)->latestOfMany();
    }

    public function verifikasiSkripsi(): HasMany
    {
        return $this->hasMany(VerifikasiSkripsi::class);
    }

    public function latestVerifikasi(): HasOne
    {
        return $this->hasOne(VerifikasiSkripsi::class)->latestOfMany();
    }

    public function savedBy(): HasMany
    {
        return $this->hasMany(SavedSkripsi::class);
    }

    public function bookmarkedByMahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'saved_skripsi', 'skripsi_id', 'mahasiswa_id')
            ->withPivot('created_at');
    }
}
