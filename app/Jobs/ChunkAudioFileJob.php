<?php

namespace App\Jobs;

use App\Models\AudioFile;
use App\Models\Book;
use App\Services\AudioProcessingService;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChunkAudioFileJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public AudioFile $audioFile) {}

    public function handle(AudioProcessingService $processor): void
    {
        try {
            $chunks = $processor->chunk($this->audioFile);
        } catch (Throwable $e) {
            Log::error('Audio chunking failed', ['audio_file_id' => $this->audioFile->id, 'error' => $e->getMessage()]);

            $this->audioFile->update(['status' => AudioFile::STATUS_FAILED]);
            $this->audioFile->book->update(['status' => Book::STATUS_FAILED]);

            return;
        }

        if (empty($chunks)) {
            $this->audioFile->update(['status' => AudioFile::STATUS_FAILED]);
            $this->audioFile->book->update(['status' => Book::STATUS_FAILED]);

            return;
        }

        $book = $this->audioFile->book;
        $book->update(['status' => Book::STATUS_TRANSCRIBING]);

        $jobs = collect($chunks)->map(fn ($chunk) => new TranscribeAudioChunkJob($chunk))->all();

        Bus::batch($jobs)
            ->allowFailures()
            ->then(function (Batch $batch) use ($book) {
                MergeTranscriptsJob::dispatch($book);
            })
            ->catch(function (Batch $batch, Throwable $e) use ($book) {
                Log::error('Transcription batch failed', ['book_id' => $book->id, 'error' => $e->getMessage()]);
            })
            ->finally(function (Batch $batch) {
                //
            })
            ->name("transcribe-book-{$book->id}")
            ->dispatch();
    }
}
