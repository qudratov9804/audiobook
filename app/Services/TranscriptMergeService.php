<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Transcript;
use Illuminate\Support\Facades\Storage;

class TranscriptMergeService
{
    public function merge(Book $book): Transcript
    {
        $chunkTranscripts = Transcript::query()
            ->whereHas('audioChunk.audioFile.book', fn ($q) => $q->whereKey($book->id))
            ->where('is_final', false)
            ->with('audioChunk')
            ->get()
            ->sortBy(fn (Transcript $transcript) => $transcript->audioChunk->chunk_index)
            ->values();

        $fullText = $chunkTranscripts->pluck('content')->filter()->implode("\n\n");

        $segments = $chunkTranscripts->map(fn (Transcript $transcript) => [
            'chunk_index' => $transcript->audioChunk->chunk_index,
            'start_time' => $transcript->audioChunk->start_time,
            'end_time' => $transcript->audioChunk->end_time,
            'text' => $transcript->content,
        ])->values()->all();

        $baseName = "book-{$book->id}";
        $txtPath = "{$book->id}/{$baseName}.txt";
        $jsonPath = "{$book->id}/{$baseName}.json";

        Storage::disk('transcripts')->put($txtPath, $fullText);
        Storage::disk('transcripts')->put($jsonPath, json_encode([
            'book_id' => $book->id,
            'title' => $book->title,
            'segments' => $segments,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $book->transcripts()->create([
            'is_final' => true,
            'content' => $fullText,
            'format' => 'txt',
            'disk' => 'transcripts',
            'path' => $txtPath,
            'status' => Transcript::STATUS_COMPLETED,
        ]);
    }
}
