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

    protected static ?string $heading = 'So\'nggi faoliyat';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ActivityLog::query()->with('user')->latest()->limit(10))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Foydalanuvchi')
                    ->placeholder('Tizim'),
                TextColumn::make('action')
                    ->label('Amal')
                    ->badge(),
                TextColumn::make('description')
                    ->label('Tavsif'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Vaqti'),
            ])
            ->paginated(false);
    }
}
