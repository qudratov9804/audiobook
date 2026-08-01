<?php

namespace App\Filament\Pages;

use App\Models\FailedJob;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class QueueMonitor extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.queue-monitor';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup = 'Pipeline';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Queue Monitor';

    public function getPendingJobsCount(): int
    {
        return (int) DB::table('jobs')->count();
    }

    public function getFailedJobsCount(): int
    {
        return (int) DB::table('failed_jobs')->count();
    }

    public function getBatchesInProgress(): int
    {
        return (int) DB::table('job_batches')->whereNull('finished_at')->count();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(FailedJob::query())
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->limit(12)
                    ->copyable(),
                TextColumn::make('connection'),
                TextColumn::make('queue'),
                TextColumn::make('exception')
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('failed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('retry')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->action(fn (FailedJob $record) => Artisan::call('queue:retry', ['id' => [$record->id]]))
                    ->requiresConfirmation(),
                Action::make('delete')
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->action(fn (FailedJob $record) => $record->delete())
                    ->requiresConfirmation(),
            ])
            ->defaultSort('failed_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retryAll')
                ->label('Retry all failed')
                ->icon(Heroicon::OutlinedArrowPath)
                ->action(fn () => Artisan::call('queue:retry', ['id' => ['all']]))
                ->requiresConfirmation(),
            Action::make('flushAll')
                ->label('Flush all failed')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->action(fn () => Artisan::call('queue:flush'))
                ->requiresConfirmation(),
        ];
    }
}
