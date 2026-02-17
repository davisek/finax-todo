<?php

namespace Modules\Todo\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'TodoSimpleResource')]
class TodoSimpleResourceSchema
{
    #[OA\Property(property: 'id', type: 'integer', example: 1)]
    public int $id;

    #[OA\Property(property: 'title', type: 'string', example: 'Buy groceries')]
    public string $title;

    #[OA\Property(property: 'completed', type: 'boolean', example: false)]
    public bool $completed;

    #[OA\Property(property: 'created_at', type: 'string', example: '2026-02-17 12:00:00')]
    public ?string $created_at;
}
