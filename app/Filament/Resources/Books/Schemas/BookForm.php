<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Sarlavha')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('author')
                    ->label('Muallif')
                    ->maxLength(255),
                Select::make('user_id')
                    ->label('Foydalanuvchi')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category_id')
                    ->label('Kategoriya')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('language_id')
                    ->label('Til')
                    ->relationship('language', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('duration')
                    ->label('Davomiyligi')
                    ->numeric()
                    ->suffix('soniya'),
                Textarea::make('description')
                    ->label('Tavsif')
                    ->columnSpanFull()
                    ->rows(4),
            ])
            ->columns(2);
    }
}
