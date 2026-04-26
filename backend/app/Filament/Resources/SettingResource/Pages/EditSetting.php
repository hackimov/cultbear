<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Pages\ManageLegalDetails;
use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        if ($this->getRecord()->key === 'legal_details') {
            $this->redirect(ManageLegalDetails::getUrl());
        }

        $this->authorizeAccess();
        $this->fillForm();
        $this->previousUrl = url()->previous();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
