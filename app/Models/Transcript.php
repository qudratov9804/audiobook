<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'book_id', 'audio_chunk_id', 'is_final', 'content',
    'format', 'disk', 'path', 'status',
])]
class Transcript extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected function casts(): array
    {
        return [
            'is_final' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Book, $this>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * @return BelongsTo<AudioChunk, $this>
     */
    public function audioChunk(): BelongsTo
    {
        return $this->belongsTo(AudioChunk::class);
    }

    public function storage(): FilesystemAdapter
    {
        return Storage::disk($this->disk);
    }
}
