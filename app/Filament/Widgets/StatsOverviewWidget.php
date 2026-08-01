<?php

namespace App\Filament\Widgets;

use App\Models\AudioFile;
use App\Models\Book;
use App\Models\Transcript;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::query()->count()),
            Stat::make('Total Books', Book::query()->count()),
            Stat::make('Total Audio Files', AudioFile::query()->count()),
            Stat::make('Total Transcripts', Transcript::query()->where('is_final', true)->count()),
            Stat::make('Storage Usage', Number::fileSize($this->storageUsage())),
            Stat::make('Failed Jobs', DB::table('failed_jobs')->count())
                ->color(fn () => DB::table('failed_jobs')->count() > 0 ? 'danger' : 'success'),
        ];
    }

    protected function storageUsage(): int
    {
        $total = 0;

        foreach (['books', 'audio', 'chunks', 'transcripts'] as $disk) {
            $path = storage_path("app/{$disk}");

            if (! is_dir($path)) {
                continue;
            }

            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
                $total += $file->getSize();
            }
        }

        return $total;
    }
}
