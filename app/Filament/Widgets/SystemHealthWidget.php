<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SystemHealthWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('PHP versiyasi', PHP_VERSION),
            Stat::make('Bo\'sh disk xotirasi', Number::fileSize(disk_free_space(base_path()) ?: 0)),
            Stat::make('Navbat ulanishi', config('queue.default')),
        ];
    }
}
