<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DailyUploadsChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Kunlik yuklamalar (oxirgi 14 kun)';

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn (int $i) => Carbon::today()->subDays($i));

        $counts = Book::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::today()->subDays(13))
            ->groupBy('date')
            ->pluck('total', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'Yuklamalar',
                    'data' => $days->map(fn (Carbon $day) => $counts[$day->toDateString()] ?? 0)->all(),
                ],
            ],
            'labels' => $days->map(fn (Carbon $day) => $day->format('M j'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
