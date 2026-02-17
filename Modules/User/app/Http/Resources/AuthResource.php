<?php

namespace Modules\User\Http\Resources;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class AuthResource extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public ?Carbon $created_at,
    ) {
    }
}
