<?php

namespace Modules\Todo\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'TodoStatsResource')]
class TodoStatsResourceSchema
{
    #[OA\Property(property: 'total', type: 'integer', example: 25)]
    public int $total;

    #[OA\Property(property: 'completed', type: 'integer', example: 10)]
    public int $completed;

    #[OA\Property(property: 'pending', type: 'integer', example: 15)]
    public int $pending;
}
