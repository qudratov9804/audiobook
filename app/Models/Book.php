<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'user_id', 'category_id', 'language_id', 'title', 'slug',
    'author', 'description', 'cover_path', 'duration',
])]
class Book extends Model
{
    use HasFactory, SoftDeletes;

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
     * @return HasMany<BookSection, $this>
     */
    public function sections(): HasMany
    {
        return $this->hasMany(BookSection::class);
    }
}
