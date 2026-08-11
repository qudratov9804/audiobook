<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
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
                FileUpload::make('cover_path')
                    ->label('Muqova rasmi')
                    ->image()
                    ->disk('public')
                    ->directory('covers')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->helperText('Tavsiya etiladigan o\'lcham: 600 × 900 px (2:3 nisbat). JPG yoki PNG, hajmi 2 MB dan oshmasin.')
                    ->required(fn (string $operation): bool => $operation === 'create')
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
                Select::make('rating')
                    ->label('Reyting')
                    ->options([
                        0 => 'Baholanmagan',
                        1 => '⭐',
                        2 => '⭐⭐',
                        3 => '⭐⭐⭐',
                        4 => '⭐⭐⭐⭐',
                        5 => '⭐⭐⭐⭐⭐',
                    ])
                    ->default(0)
                    ->native(false),
                Textarea::make('description')
                    ->label('Tavsif')
                    ->columnSpanFull()
                    ->rows(4),
            ])
            ->columns(2);
    }
}
