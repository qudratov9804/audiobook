<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('group')
                    ->label('Guruh')
                    ->required()
                    ->default('general'),
                TextInput::make('key')
                    ->label('Kalit')
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('value')
                    ->label('Qiymat')
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Turi')
                    ->options([
                        'string' => 'Matn',
                        'integer' => 'Butun son',
                        'boolean' => 'Mantiqiy (ha/yo\'q)',
                        'json' => 'JSON',
                    ])
                    ->required()
                    ->default('string'),
            ]);
    }
}
