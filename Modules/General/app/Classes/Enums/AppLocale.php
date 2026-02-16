<?php

namespace Modules\General\Classes\Enums;

enum AppLocale: string
{
    case SLOVAK = 'sk';
    case CZECH = 'cs';
    case ENGLISH = 'en';

    public static function default(): self
    {
        return self::SLOVAK;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
