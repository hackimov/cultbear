<?php

namespace App\Filament\Resources\Concerns;

use Illuminate\Support\Str;

/**
 * Заголовок страницы создания: «Создать …» с нормальным русским падежом и регистром.
 */
trait HasAccusativeCreateTitle
{
    /**
     * Винительный падеж для «Создать …» (напр. «пользователя»). По умолчанию — строчный model label.
     */
    protected static function createAccusativeLabel(): ?string
    {
        return null;
    }

    public static function getTitleCaseModelLabel(): string
    {
        $acc = static::createAccusativeLabel();
        if (filled($acc)) {
            return $acc;
        }

        if (! static::hasTitleCaseModelLabel()) {
            return Str::lower(static::getModelLabel());
        }

        return Str::lower(static::getModelLabel());
    }
}
