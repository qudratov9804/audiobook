<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Models\Book;
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
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('language_id')
                    ->relationship('language', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('author')
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('cover_path')
                    ->image()
                    ->directory('covers')
                    ->disk('public'),
                Select::make('status')
                    ->options([
                        Book::STATUS_PENDING => 'Pending',
                        Book::STATUS_PROCESSING => 'Processing',
                        Book::STATUS_TRANSCRIBING => 'Transcribing',
                        Book::STATUS_COMPLETED => 'Completed',
                        Book::STATUS_FAILED => 'Failed',
                    ])
                    ->required()
                    ->default(Book::STATUS_PENDING),
                TextInput::make('duration')
                    ->numeric()
                    ->suffix('seconds'),
            ]);
    }
}
