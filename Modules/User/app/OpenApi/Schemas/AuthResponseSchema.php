<?php

namespace Modules\User\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'AuthResponse')]
class AuthResponseSchema
{
    #[OA\Property(property: 'user', ref: '#/components/schemas/AuthResource')]
    public mixed $user;

    #[OA\Property(property: 'access_token', type: 'string', example: '1|abc123...')]
    public string $access_token;

    #[OA\Property(property: 'expires_in', type: 'integer', example: 900)]
    public int $expires_in;
}
