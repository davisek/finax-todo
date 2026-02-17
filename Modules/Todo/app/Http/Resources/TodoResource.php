<?php

namespace Modules\Todo\Http\Resources;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class TodoResource extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public bool $completed,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public ?Carbon $created_at,
    ) {}
}
