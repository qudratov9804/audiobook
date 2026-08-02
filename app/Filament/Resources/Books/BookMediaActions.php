<?php

namespace App\Filament\Resources\Books;

use App\Models\Book;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;

class BookMediaActions
{
    public static function uploadCover(): Action
    {
        return Action::make('uploadCover')
            ->label('Muqova yuklash')
            ->icon(Heroicon::OutlinedPhoto)
            ->color('gray')
            ->schema([
                FileUpload::make('cover_path')
                    ->label('Muqova rasmi')
                    ->image()
                    ->disk('public')
                    ->directory('covers')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->helperText('Tavsiya etiladigan o\'lcham: 600 × 900 px (2:3 nisbat). JPG yoki PNG, hajmi 2 MB dan oshmasin.')
                    ->required(),
            ])
            ->action(function (array $data, Book $record): void {
                $path = Arr::wrap($data['cover_path'])[0] ?? null;

                $record->update(['cover_path' => $path]);

                Notification::make()
                    ->title('Muqova rasmi saqlandi')
                    ->success()
                    ->send();
            });
    }
}
