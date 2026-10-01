<?php

namespace App\Models;

use App\Enums\VerifikasiStatus;
use Database\Factories\VerifikasiSkripsiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'skripsi_id',
    'diverifikasi_oleh',
    'status',
    'catatan',
    'verified_at',
])]
class VerifikasiSkripsi extends Model
{
    /** @use HasFactory<VerifikasiSkripsiFactory> */
    use HasFactory;

    protected $table = 'verifikasi_skripsi';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => VerifikasiStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function skripsi(): BelongsTo
    {
        return $this->belongsTo(Skripsi::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
