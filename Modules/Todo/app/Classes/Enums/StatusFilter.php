<?php

namespace Modules\Todo\Classes\Enums;

enum StatusFilter: string
{
    case COMPLETED = 'completed';
    case PENDING = 'pending';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
