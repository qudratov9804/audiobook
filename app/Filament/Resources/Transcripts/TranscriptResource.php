<?php

namespace App\Filament\Resources\Transcripts;

use App\Filament\Resources\Transcripts\Pages\ListTranscripts;
use App\Filament\Resources\Transcripts\Pages\ViewTranscript;
use App\Filament\Resources\Transcripts\Schemas\TranscriptInfolist;
use App\Filament\Resources\Transcripts\Tables\TranscriptsTable;
use App\Models\Transcript;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TranscriptResource extends Resource
{
    protected static ?string $model = Transcript::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Pipeline';

    protected static ?int $navigationSort = 2;

    public static function infolist(Schema $schema): Schema
    {
        return TranscriptInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TranscriptsTable::configure($table);
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
            'index' => ListTranscripts::route('/'),
            'view' => ViewTranscript::route('/{record}'),
        ];
    }
}
