<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentActivityWidget extends TableWidget
{
    protected static ?int $sort = 6;

    protected static ?string $heading = 'Recent Activity';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ActivityLog::query()->with('user')->latest()->limit(10))
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('System'),
                TextColumn::make('action')
                    ->badge(),
                TextColumn::make('description'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('When'),
            ])
            ->paginated(false);
    }
}
