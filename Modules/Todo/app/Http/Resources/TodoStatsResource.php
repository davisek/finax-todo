<?php

namespace Modules\Todo\Http\Resources;

use Spatie\LaravelData\Data;

class TodoStatsResource extends Data
{
    public function __construct(
        public int $total,
        public int $completed,
        public int $pending,
    ) {}
}
