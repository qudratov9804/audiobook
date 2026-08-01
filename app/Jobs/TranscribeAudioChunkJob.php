<?php

namespace App\Jobs;

use App\Models\AudioChunk;
use App\Services\WhisperTranscriptionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class TranscribeAudioChunkJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 1800;

    public function __construct(public AudioChunk $chunk) {}

    public function handle(WhisperTranscriptionService $whisper): void
    {
        $this->chunk->update(['status' => AudioChunk::STATUS_PROCESSING]);

        try {
            $text = $whisper->transcribe($this->chunk);

            $this->chunk->transcript()->updateOrCreate([], [
                'book_id' => $this->chunk->audioFile->book_id,
                'is_final' => false,
                'content' => $text,
                'format' => 'txt',
                'status' => 'completed',
            ]);

            $this->chunk->update(['status' => AudioChunk::STATUS_TRANSCRIBED]);
        } catch (Throwable $e) {
            Log::error('Chunk transcription failed', ['chunk_id' => $this->chunk->id, 'error' => $e->getMessage()]);

            $this->chunk->update([
                'status' => AudioChunk::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
