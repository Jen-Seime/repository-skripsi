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
     * Boot the model.
     */
    protected static function booted(): void
    {
        // Saat update: hapus file lama dari storage jika path berubah
        static::updating(function (SkripsiFile $file) {
            if ($file->isDirty('path') && $file->getOriginal('path')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file->getOriginal('path'));
            }
        });

        // Saat delete: hapus file dari storage
        static::deleting(function (SkripsiFile $file) {
            if ($file->path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file->path);
            }
        });
    }

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
