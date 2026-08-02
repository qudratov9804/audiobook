<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\BookSection;
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
            Stat::make('Jami foydalanuvchilar', User::query()->count()),
            Stat::make('Jami kitoblar', Book::query()->count()),
            Stat::make('Jami bo\'limlar', BookSection::query()->count()),
            Stat::make('Xotira sarfi', Number::fileSize($this->storageUsage())),
            Stat::make('Muvaffaqiyatsiz vazifalar', DB::table('failed_jobs')->count())
                ->color(fn () => DB::table('failed_jobs')->count() > 0 ? 'danger' : 'success'),
        ];
    }

    protected function storageUsage(): int
    {
        $total = 0;

        foreach (['books', 'audio'] as $disk) {
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
