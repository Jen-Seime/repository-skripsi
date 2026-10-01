<?php

namespace App\Models;

use App\Enums\SkripsiFileType;
use Database\Factories\SkripsiFileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'skripsi_id',
    'jenis',
    'nama_file',
    'path',
    'ukuran',
    'mime_type',
])]
class SkripsiFile extends Model
{
    /** @use HasFactory<SkripsiFileFactory> */
    use HasFactory;

    protected $table = 'skripsi_files';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ukuran' => 'integer',
            'jenis' => SkripsiFileType::class,
        ];
    }

    public function skripsi(): BelongsTo
    {
        return $this->belongsTo(Skripsi::class);
    }
}
