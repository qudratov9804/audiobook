<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\Schemas\UserForm;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ism')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Elektron pochta manzili')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => UserForm::roleOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => $state === User::ROLE_ADMIN ? 'danger' : 'gray'),
                IconColumn::make('is_active')
                    ->label('Faol')
                    ->boolean(),
                TextColumn::make('books_count')
                    ->counts('books')
                    ->label('Kitoblar'),
                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rol')
                    ->options(UserForm::roleOptions()),
                TernaryFilter::make('is_active')
                    ->label('Faol'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
