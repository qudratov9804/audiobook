<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Foydalanuvchi')
                    ->placeholder('Tizim'),
                TextEntry::make('action')
                    ->label('Amal'),
                TextEntry::make('subject_type')
                    ->label('Obyekt turi')
                    ->placeholder('-'),
                TextEntry::make('subject_id')
                    ->label('Obyekt ID')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->label('Tavsif')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('properties')
                    ->label('Qo\'shimcha ma\'lumot')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->label('IP manzil')
                    ->placeholder('-'),
                TextEntry::make('user_agent')
                    ->label('Brauzer/qurilma')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Yangilangan')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
