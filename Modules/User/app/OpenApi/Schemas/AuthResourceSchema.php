<?php

namespace Modules\User\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'AuthResource')]
class AuthResourceSchema
{
    #[OA\Property(property: 'id', type: 'integer', example: 1)]
    public int $id;

    #[OA\Property(property: 'name', type: 'string', example: 'Test User')]
    public string $name;

    #[OA\Property(property: 'email', type: 'string', example: 'test@gmail.com')]
    public string $email;

    #[OA\Property(property: 'created_at', type: 'string', example: '2026-02-17 12:00:00')]
    public ?string $created_at;
}
