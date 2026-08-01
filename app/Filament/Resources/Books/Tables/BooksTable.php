<?php

namespace App\Filament\Resources\Books\Tables;

use App\Models\Book;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_path')
                    ->disk('public')
                    ->label(''),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('author')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Uploaded by')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->badge()
                    ->searchable(),
                TextColumn::make('language.name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Book::STATUS_COMPLETED => 'success',
                        Book::STATUS_FAILED => 'danger',
                        Book::STATUS_PENDING => 'gray',
                        default => 'warning',
                    }),
                TextColumn::make('duration')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (?int $state) => $state ? gmdate('H:i:s', $state) : '—'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Book::STATUS_PENDING => 'Pending',
                        Book::STATUS_PROCESSING => 'Processing',
                        Book::STATUS_TRANSCRIBING => 'Transcribing',
                        Book::STATUS_COMPLETED => 'Completed',
                        Book::STATUS_FAILED => 'Failed',
                    ]),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                SelectFilter::make('language_id')
                    ->relationship('language', 'name')
                    ->label('Language'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
