<?php

namespace App\Jobs;

use App\Models\AudioFile;
use App\Models\Book;
use App\Services\TranscriptMergeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class MergeTranscriptsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public Book $book) {}

    public function handle(TranscriptMergeService $merger): void
    {
        try {
            $merger->merge($this->book);

            $audioFile = $this->book->audioFiles()->latest()->first();
            $audioFile?->update(['status' => AudioFile::STATUS_READY]);

            $this->book->update([
                'status' => Book::STATUS_COMPLETED,
                'duration' => $audioFile?->duration,
            ]);
        } catch (Throwable $e) {
            Log::error('Transcript merge failed', ['book_id' => $this->book->id, 'error' => $e->getMessage()]);

            $this->book->update(['status' => Book::STATUS_FAILED]);

            throw $e;
        }
    }
}
