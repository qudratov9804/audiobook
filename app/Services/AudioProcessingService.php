<?php

namespace App\Services;

use App\Models\AudioChunk;
use App\Models\AudioFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

class AudioProcessingService
{
    public function probeDuration(string $absolutePath): int
    {
        $process = new Process([
            config('audio.ffprobe_path'),
            '-v', 'error',
            '-show_entries', 'format=duration',
            '-of', 'default=noprint_wrappers=1:nokey=1',
            $absolutePath,
        ]);

        $process->setTimeout(120);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException("ffprobe failed: {$process->getErrorOutput()}");
        }

        return (int) round((float) trim($process->getOutput()));
    }

    /**
     * Split an audio file into fixed-duration chunks and persist AudioChunk rows.
     *
     * @return array<int, AudioChunk>
     */
    public function chunk(AudioFile $audioFile): array
    {
        $sourcePath = Storage::disk($audioFile->disk)->path($audioFile->path);

        if (! is_file($sourcePath)) {
            throw new RuntimeException("Source audio file not found: {$sourcePath}");
        }

        $duration = $this->probeDuration($sourcePath);
        $chunkDuration = config('audio.chunk_duration');
        $format = $audioFile->format ?: pathinfo($sourcePath, PATHINFO_EXTENSION);

        $outputDir = "{$audioFile->id}";
        $absoluteOutputDir = Storage::disk('chunks')->path($outputDir);

        if (! is_dir($absoluteOutputDir)) {
            mkdir($absoluteOutputDir, 0755, true);
        }

        $pattern = $absoluteOutputDir.DIRECTORY_SEPARATOR.'chunk_%03d.'.$format;

        $process = new Process([
            config('audio.ffmpeg_path'),
            '-y',
            '-i', $sourcePath,
            '-f', 'segment',
            '-segment_time', (string) $chunkDuration,
            '-c', 'copy',
            '-reset_timestamps', '1',
            $pattern,
        ]);

        $process->setTimeout(3600);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException("ffmpeg chunking failed: {$process->getErrorOutput()}");
        }

        $files = collect(glob($absoluteOutputDir.DIRECTORY_SEPARATOR.'chunk_*.'.$format))->sort()->values();

        $audioFile->chunks()->delete();

        $chunks = [];
        foreach ($files as $index => $absoluteFile) {
            $startTime = $index * $chunkDuration;
            $endTime = min($startTime + $chunkDuration, $duration ?: ($startTime + $chunkDuration));

            $chunks[] = $audioFile->chunks()->create([
                'chunk_index' => $index,
                'disk' => 'chunks',
                'path' => $outputDir.DIRECTORY_SEPARATOR.basename($absoluteFile),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration' => $endTime - $startTime,
                'status' => AudioChunk::STATUS_PENDING,
            ]);
        }

        $audioFile->update(['duration' => $duration]);

        return $chunks;
    }
}
