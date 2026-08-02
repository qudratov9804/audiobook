<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

#[Fillable(['book_id', 'name', 'description', 'disk', 'path', 'format', 'duration', 'size'])]
class BookSection extends Model
{
    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (BookSection $section): void {
            if ($section->path) {
                $section->storage()->delete($section->path);
            }
        });
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
}
