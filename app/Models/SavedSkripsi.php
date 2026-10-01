<?php

namespace App\Models;

use Database\Factories\SavedSkripsiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'mahasiswa_id',
    'skripsi_id',
])]
class SavedSkripsi extends Model
{
    /** @use HasFactory<SavedSkripsiFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'saved_skripsi';

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function skripsi(): BelongsTo
    {
        return $this->belongsTo(Skripsi::class);
    }
}
