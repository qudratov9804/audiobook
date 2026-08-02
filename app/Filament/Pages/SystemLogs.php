<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\File;
use UnitEnum;

class SystemLogs extends Page
{
    protected string $view = 'filament.pages.system-logs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentMagnifyingGlass;

    protected static string|UnitEnum|null $navigationGroup = 'Boshqaruv';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Tizim jurnali';

    protected static ?string $navigationLabel = 'Tizim jurnali';

    public int $lines = 200;

    public function getLogContent(): string
    {
        $path = storage_path('logs/laravel.log');

        if (! File::exists($path)) {
            return 'Jurnal fayli topilmadi.';
        }

        $content = File::get($path);
        $allLines = preg_split('/\r\n|\r|\n/', $content);

        return implode("\n", array_slice($allLines, -$this->lines));
    }

    public function getLogSize(): string
    {
        $path = storage_path('logs/laravel.log');

        if (! File::exists($path)) {
            return '0 KB';
        }

        return number_format(File::size($path) / 1024, 2).' KB';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Yangilash')
                ->icon(Heroicon::OutlinedArrowPath)
                ->action(fn () => null),
            Action::make('clear')
                ->label('Jurnalni tozalash')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $path = storage_path('logs/laravel.log');

                    if (File::exists($path)) {
                        File::put($path, '');
                    }
                }),
        ];
    }
}
