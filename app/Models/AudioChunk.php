<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'audio_file_id', 'chunk_index', 'disk', 'path',
    'start_time', 'end_time', 'duration', 'status', 'error_message',
])]
class AudioChunk extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_TRANSCRIBED = 'transcribed';

    public const STATUS_FAILED = 'failed';

    protected function casts(): array
    {
        return [
            'chunk_index' => 'integer',
            'start_time' => 'integer',
            'end_time' => 'integer',
            'duration' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<AudioFile, $this>
     */
    public function audioFile(): BelongsTo
    {
        return $this->belongsTo(AudioFile::class);
    }

    /**
     * @return HasOne<Transcript, $this>
     */
    public function transcript(): HasOne
    {
        return $this->hasOne(Transcript::class);
    }

    public function storage(): FilesystemAdapter
    {
        return Storage::disk($this->disk);
    }
}
