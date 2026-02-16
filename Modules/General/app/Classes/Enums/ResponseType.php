<?php

namespace Modules\General\Classes\Enums;

enum ResponseType: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case SUCCESS = 'success';
    case ERROR = 'error';
}
