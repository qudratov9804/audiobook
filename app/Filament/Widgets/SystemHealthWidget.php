<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SystemHealthWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('PHP Version', PHP_VERSION),
            Stat::make('Disk Free Space', Number::fileSize(disk_free_space(base_path()) ?: 0)),
            Stat::make('Queue Connection', config('queue.default')),
            Stat::make('FFmpeg', $this->binaryAvailable(config('audio.ffmpeg_path')) ? 'Available' : 'Not found')
                ->color($this->binaryAvailable(config('audio.ffmpeg_path')) ? 'success' : 'danger'),
            Stat::make('Whisper', $this->binaryAvailable(config('audio.whisper.binary')) ? 'Available' : 'Not found')
                ->color($this->binaryAvailable(config('audio.whisper.binary')) ? 'success' : 'danger'),
        ];
    }

    protected function binaryAvailable(string $binary): bool
    {
        $finder = Str::startsWith(PHP_OS, 'WIN') ? 'where' : 'which';

        $process = new Process([$finder, $binary]);
        $process->run();

        return $process->isSuccessful();
    }
}
