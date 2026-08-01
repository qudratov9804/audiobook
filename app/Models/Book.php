<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'user_id', 'category_id', 'language_id', 'title', 'slug',
    'author', 'description', 'cover_path', 'status', 'duration',
])]
class Book extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_TRANSCRIBING = 'transcribing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Book $book) {
            if (blank($book->slug)) {
                $book->slug = Str::slug($book->title).'-'.Str::random(6);
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Language, $this>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * @return HasMany<BookFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(BookFile::class);
    }

    /**
     * @return HasMany<AudioFile, $this>
     */
    public function audioFiles(): HasMany
    {
        return $this->hasMany(AudioFile::class);
    }

    /**
     * @return HasOne<AudioFile, $this>
     */
    public function audioFile(): HasOne
    {
        return $this->hasOne(AudioFile::class)->latestOfMany();
    }

    /**
     * @return HasMany<Transcript, $this>
     */
    public function transcripts(): HasMany
    {
        return $this->hasMany(Transcript::class);
    }

    /**
     * @return HasOne<Transcript, $this>
     */
    public function finalTranscript(): HasOne
    {
        return $this->hasOne(Transcript::class)->where('is_final', true)->latestOfMany();
    }
}
