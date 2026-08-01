<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'book_id', 'type', 'disk', 'path', 'original_filename',
    'mime_type', 'extension', 'size',
])]
class BookFile extends Model
{
    public const TYPE_PDF = 'pdf';

    public const TYPE_DOCX = 'docx';

    public const TYPE_TXT = 'txt';

    public const TYPE_MP3 = 'mp3';

    public const TYPE_WAV = 'wav';

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Book, $this>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function storage(): FilesystemAdapter
    {
        return Storage::disk($this->disk);
    }

    public function isAudio(): bool
    {
        return in_array($this->type, [self::TYPE_MP3, self::TYPE_WAV], true);
    }
}
