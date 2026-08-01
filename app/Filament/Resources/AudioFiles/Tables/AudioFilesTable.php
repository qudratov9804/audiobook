<?php

namespace App\Filament\Resources\AudioFiles\Tables;

use App\Models\AudioFile;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AudioFilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('book.title')
                    ->label('Book')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('format')
                    ->badge(),
                TextColumn::make('duration')
                    ->formatStateUsing(fn (?int $state) => $state ? gmdate('H:i:s', $state) : '—'),
                TextColumn::make('size')
                    ->formatStateUsing(fn (int $state) => number_format($state / 1024 / 1024, 2).' MB')
                    ->sortable(),
                TextColumn::make('chunks_count')
                    ->counts('chunks')
                    ->label('Chunks'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        AudioFile::STATUS_READY => 'success',
                        AudioFile::STATUS_FAILED => 'danger',
                        AudioFile::STATUS_PENDING => 'gray',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        AudioFile::STATUS_PENDING => 'Pending',
                        AudioFile::STATUS_PROCESSING => 'Processing',
                        AudioFile::STATUS_READY => 'Ready',
                        AudioFile::STATUS_FAILED => 'Failed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
