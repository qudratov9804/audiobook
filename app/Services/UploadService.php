<?php

namespace App\Services;

use App\Models\AudioFile;
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
        $isAudio = in_array($extension, config('audio.allowed_audio_mimes'), true);
        $isDocument = in_array($extension, config('audio.allowed_document_mimes'), true);

        if (! $isAudio && ! $isDocument) {
            throw new RuntimeException("Unsupported file type: {$extension}");
        }

        $filename = Str::uuid()->toString().'.'.$extension;
        $path = "{$book->id}/{$filename}";

        Storage::disk('books')->putFileAs((string) $book->id, $file, $filename);

        $bookFile = $book->files()->create([
            'type' => $extension,
            'disk' => 'books',
            'path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
        ]);

        if ($isAudio) {
            $book->audioFiles()->create([
                'disk' => 'books',
                'path' => $path,
                'format' => $extension,
                'size' => $file->getSize(),
                'status' => AudioFile::STATUS_PENDING,
            ]);
        }

        $this->activityLog->log('book.file_uploaded', $book, "File \"{$bookFile->original_filename}\" uploaded for \"{$book->title}\"");

        return $bookFile;
    }
}
