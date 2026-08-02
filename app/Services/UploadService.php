<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class UploadService
{
    public function __construct(protected ActivityLogService $activityLog) {}

    public function store(Book $book, UploadedFile $file): BookFile
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $filename = Str::uuid()->toString().'.'.$extension;
        $path = "{$book->id}/{$filename}";

        Storage::disk('books')->putFileAs((string) $book->id, $file, $filename);

        return $this->attach(
            $book,
            $path,
            $extension,
            $file->getClientOriginalName(),
            $file->getMimeType(),
            $file->getSize(),
        );
    }

    /**
     * Attach a file that has already been written to the "books" disk (e.g. by a
     * Filament FileUpload field) to a book, moving it into the book's own folder.
     */
    public function attachStoredFile(Book $book, string $storedRelativePath, ?string $originalFilename = null): BookFile
    {
        $extension = strtolower(pathinfo($storedRelativePath, PATHINFO_EXTENSION));
        $filename = Str::uuid()->toString().'.'.$extension;
        $path = "{$book->id}/{$filename}";

        Storage::disk('books')->move($storedRelativePath, $path);

        return $this->attach(
            $book,
            $path,
            $extension,
            $originalFilename ?: basename($storedRelativePath),
            Storage::disk('books')->mimeType($path) ?: null,
            Storage::disk('books')->size($path),
        );
    }

    protected function attach(Book $book, string $path, string $extension, string $originalFilename, ?string $mimeType, int $size): BookFile
    {
        $isAudio = in_array($extension, config('audio.allowed_audio_mimes'), true);
        $isDocument = in_array($extension, config('audio.allowed_document_mimes'), true);

        if (! $isAudio && ! $isDocument) {
            throw new RuntimeException("Unsupported file type: {$extension}");
        }

        $bookFile = $book->files()->create([
            'type' => $extension,
            'disk' => 'books',
            'path' => $path,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $size,
        ]);

        if ($isAudio) {
            $book->sections()->create([
                'disk' => 'books',
                'path' => $path,
                'format' => $extension,
                'size' => $size,
            ]);
        }

        $this->activityLog->log('book.file_uploaded', $book, "File \"{$bookFile->original_filename}\" uploaded for \"{$book->title}\"");

        return $bookFile;
    }
}
