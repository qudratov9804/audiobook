<?php

namespace App\Filament\Resources\Books\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Bo\'limlar';

    protected static ?string $modelLabel = 'bo\'lim';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Bo\'lim nomi')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Tasnifi')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('path')
                    ->label('Audio fayl')
                    ->disk('books')
                    ->directory(fn (): string => $this->getOwnerRecord()->id.'/sections')
                    ->visibility('private')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav', 'audio/wave'])
                    ->maxSize(512000)
                    ->helperText('MP3 yoki WAV, hajmi 500 MB dan oshmasin.')
                    ->downloadable()
                    ->previewable(false)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Bo\'lim nomi')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('description')
                    ->label('Tasnifi')
                    ->limit(60)
                    ->toggleable(),
                TextColumn::make('format')
                    ->label('Format')
                    ->badge(),
                TextColumn::make('size')
                    ->label('Hajmi')
                    ->formatStateUsing(fn (int $state) => number_format($state / 1024 / 1024, 2).' MB'),
                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus)
                    ->mutateFormDataUsing(function (array $data): array {
                        return $this->fillFileMetadata($data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        return $this->fillFileMetadata($data);
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at');
    }

    protected function fillFileMetadata(array $data): array
    {
        if (empty($data['path'])) {
            return $data;
        }

        $disk = Storage::disk('books');

        $data['disk'] = 'books';
        $data['format'] = strtolower(pathinfo($data['path'], PATHINFO_EXTENSION));
        $data['size'] = $disk->exists($data['path']) ? $disk->size($data['path']) : 0;

        return $data;
    }
}
