<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function roleOptions(): array
    {
        return [
            User::ROLE_ADMIN => 'Administrator',
            User::ROLE_USER => 'Foydalanuvchi',
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Ism')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Elektron pochta manzili')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Parol')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(255)
                    ->revealable(),
                Select::make('role')
                    ->label('Rol')
                    ->options(self::roleOptions())
                    ->required()
                    ->default(User::ROLE_USER),
                Toggle::make('is_active')
                    ->label('Faol')
                    ->default(true)
                    ->required(),
            ]);
    }
}
