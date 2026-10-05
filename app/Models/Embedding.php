<?php

namespace App\Models;

use Database\Factories\EmbeddingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'skripsi_id',
    'model',
    'dimension',
    'embedding',
])]
class Embedding extends Model
{
    /** @use HasFactory<EmbeddingFactory> */
    use HasFactory;

    protected $table = 'embeddings';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dimension' => 'integer',
            'embedding' => 'json',
        ];
    }

    public function skripsi(): BelongsTo
    {
        return $this->belongsTo(Skripsi::class);
    }
}
