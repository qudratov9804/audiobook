<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Foydalanuvchi')
                    ->placeholder('Tizim')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Amal')
                    ->badge()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Tavsif')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP manzil')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Sana')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Amal')
                    ->options(fn () => \App\Models\ActivityLog::query()
                        ->distinct()
                        ->pluck('action', 'action')
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
