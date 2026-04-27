<?php

namespace App\Filament\Resources\HomeThemeResource\Pages;

use App\Filament\Resources\HomeThemeResource;
use App\Models\Theme;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeThemes extends ListRecords
{
    protected static string $resource = HomeThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Создать главную тематику')
                ->visible(fn (): bool => Theme::query()->where('is_home_theme', true)->doesntExist()),
        ];
    }
}
