<?php

namespace App\Filament\Resources\HomeThemeResource\Pages;

use App\Filament\Resources\HomeThemeResource;
use App\Filament\Resources\Pages\BaseCreateRecord;

class CreateHomeTheme extends BaseCreateRecord
{
    protected static string $resource = HomeThemeResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_home_theme'] = true;

        return $data;
    }
}
