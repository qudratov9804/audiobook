<?php

namespace App\Filament\Resources\Books\Tables;

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
                    ->label('Sarlavha')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('author')
                    ->label('Muallif')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Yukladi')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategoriya')
                    ->badge()
                    ->searchable(),
                TextColumn::make('language.name')
                    ->label('Til')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('duration')
                    ->label('Davomiyligi')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (?int $state) => $state ? gmdate('H:i:s', $state) : '—'),
                TextColumn::make('rating')
                    ->label('Reyting')
                    ->sortable()
                    ->formatStateUsing(fn (?int $state) => $state ? str_repeat('⭐', $state) : '—'),
                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Yangilangan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('O\'chirilgan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategoriya'),
                SelectFilter::make('language_id')
                    ->relationship('language', 'name')
                    ->label('Til'),
                SelectFilter::make('rating')
                    ->label('Reyting')
                    ->options([
                        0 => 'Baholanmagan',
                        1 => '⭐',
                        2 => '⭐⭐',
                        3 => '⭐⭐⭐',
                        4 => '⭐⭐⭐⭐',
                        5 => '⭐⭐⭐⭐⭐',
                    ]),
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
