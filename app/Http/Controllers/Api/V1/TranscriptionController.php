<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TranscriptResource;
use App\Models\Book;
use App\Services\ActivityLogService;
use App\Services\TranscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TranscriptionController extends Controller
{
    public function __construct(
        protected TranscriptionService $transcription,
        protected ActivityLogService $activityLog,
    ) {}

    /**
     * Start transcription
     *
     * Kicks off the audio pipeline for the book's most recent audio file:
     * FFmpeg chunking, queued Whisper transcription per chunk, then merging
     * into a final transcript.
     */
    public function transcribe(Request $request, Book $book): JsonResponse
    {
        $this->authorize('update', $book);

        $audioFile = $book->audioFiles()->latest()->first();

        if (! $audioFile) {
            throw ValidationException::withMessages([
                'audio_file' => 'This book has no uploaded audio file to transcribe.',
            ]);
        }

        $this->transcription->start($book, $audioFile);

        $this->activityLog->log('book.transcribe_started', $book, "Transcription started for \"{$book->title}\"");

        return response()->json([
            'message' => 'Transcription started.',
            'status' => $book->fresh()->status,
        ], 202);
    }

    /**
     * Get transcript
     *
     * Returns the final merged transcript for the book, once available.
     */
    public function show(Request $request, Book $book): TranscriptResource
    {
        $this->authorize('view', $book);

        $transcript = $book->finalTranscript()->first();

        abort_unless($transcript, 404, 'Transcript not available yet.');

        return new TranscriptResource($transcript);
    }
}
