<?php

namespace App\Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

/**
 * Одна кнопка «Сохранить» без типичного для Filament «Создать ещё».
 */
abstract class BaseCreateRecord extends CreateRecord
{
    protected static bool $canCreateAnother = false;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Сохранить');
    }
}
