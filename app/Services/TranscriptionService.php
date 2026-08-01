<?php

namespace App\Services;

use App\Jobs\ChunkAudioFileJob;
use App\Models\AudioFile;
use App\Models\Book;

class TranscriptionService
{
    public function start(Book $book, AudioFile $audioFile): void
    {
        $book->update(['status' => Book::STATUS_PROCESSING]);
        $audioFile->update(['status' => AudioFile::STATUS_PROCESSING]);

        ChunkAudioFileJob::dispatch($audioFile);
    }
}
