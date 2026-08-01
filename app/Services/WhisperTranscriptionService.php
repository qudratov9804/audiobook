<?php

namespace App\Services;

use App\Models\AudioChunk;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class WhisperTranscriptionService
{
    /**
     * Run Whisper on a single audio chunk and return the transcribed text.
     */
    public function transcribe(AudioChunk $chunk): string
    {
        $audioPath = Storage::disk($chunk->disk)->path($chunk->path);

        if (! is_file($audioPath)) {
            throw new RuntimeException("Chunk audio file not found: {$audioPath}");
        }

        $outputDir = storage_path('app/tmp/whisper/'.Str::uuid());
        File::ensureDirectoryExists($outputDir);

        $language = config('audio.whisper.language');

        $command = [
            config('audio.whisper.binary'),
            $audioPath,
            '--model', config('audio.whisper.model'),
            '--output_format', 'txt',
            '--output_dir', $outputDir,
        ];

        if ($language && $language !== 'auto') {
            $command[] = '--language';
            $command[] = $language;
        }

        $process = new Process($command);
        $process->setTimeout(1800);
        $process->run();

        if (! $process->isSuccessful()) {
            File::deleteDirectory($outputDir);

            throw new RuntimeException("Whisper transcription failed: {$process->getErrorOutput()}");
        }

        $textFile = $outputDir.DIRECTORY_SEPARATOR.pathinfo($audioPath, PATHINFO_FILENAME).'.txt';

        $text = is_file($textFile) ? trim(File::get($textFile)) : '';

        File::deleteDirectory($outputDir);

        return $text;
    }
}
