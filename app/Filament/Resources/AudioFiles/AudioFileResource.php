<?php

namespace App\Filament\Resources\AudioFiles;

use App\Filament\Resources\AudioFiles\Pages\ListAudioFiles;
use App\Filament\Resources\AudioFiles\Pages\ViewAudioFile;
use App\Filament\Resources\AudioFiles\Schemas\AudioFileInfolist;
use App\Filament\Resources\AudioFiles\Tables\AudioFilesTable;
use App\Models\AudioFile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AudioFileResource extends Resource
{
    protected static ?string $model = AudioFile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMusicalNote;

    protected static string|UnitEnum|null $navigationGroup = 'Pipeline';

    protected static ?int $navigationSort = 1;

    public static function infolist(Schema $schema): Schema
    {
        return AudioFileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AudioFilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAudioFiles::route('/'),
            'view' => ViewAudioFile::route('/{record}'),
        ];
    }
}
