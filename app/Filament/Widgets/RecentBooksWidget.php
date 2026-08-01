<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentBooksWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected static ?string $heading = 'Recent Uploads';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Book::query()->with(['user'])->latest()->limit(10))
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Uploaded by'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Uploaded'),
            ])
            ->paginated(false);
    }
}
