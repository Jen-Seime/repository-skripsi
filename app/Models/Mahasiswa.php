<?php

namespace App\Models;

use Database\Factories\MahasiswaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'nim', 'program_studi_id', 'angkatan'])]
class Mahasiswa extends Model
{
    /** @use HasFactory<MahasiswaFactory> */
    use HasFactory;

    protected $table = 'mahasiswa';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'angkatan' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function skripsi(): HasMany
    {
        return $this->hasMany(Skripsi::class);
    }

    public function savedSkripsi(): HasMany
    {
        return $this->hasMany(SavedSkripsi::class);
    }

    public function bookmarkedSkripsi(): BelongsToMany
    {
        return $this->belongsToMany(Skripsi::class, 'saved_skripsi', 'mahasiswa_id', 'skripsi_id')
            ->withPivot('created_at');
    }

    public function searchHistories(): HasMany
    {
        return $this->hasMany(SearchHistory::class);
    }
}
