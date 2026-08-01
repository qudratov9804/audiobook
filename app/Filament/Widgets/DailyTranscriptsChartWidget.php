<?php

namespace App\Filament\Widgets;

use App\Models\Transcript;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DailyTranscriptsChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Daily Transcripts (last 14 days)';

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn (int $i) => Carbon::today()->subDays($i));

        $counts = Transcript::query()
            ->where('is_final', true)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::today()->subDays(13))
            ->groupBy('date')
            ->pluck('total', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'Transcripts',
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
