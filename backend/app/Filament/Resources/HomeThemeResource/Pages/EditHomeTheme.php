<?php

namespace App\Filament\Resources\HomeThemeResource\Pages;

use App\Filament\Resources\HomeThemeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomeTheme extends EditRecord
{
    protected static string $resource = HomeThemeResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['banner_url']) && filter_var((string) $data['banner_url'], FILTER_VALIDATE_URL)) {
            unset($data['banner_url']);
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['banner_url']) && $this->getRecord()->banner_url && filter_var((string) $this->getRecord()->banner_url, FILTER_VALIDATE_URL)) {
            $data['banner_url'] = $this->getRecord()->banner_url;
        }

        $data['is_home_theme'] = true;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
