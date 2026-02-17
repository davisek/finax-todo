<?php

namespace Modules\General\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'AppResponse')]
class AppResponseSchema
{
    #[OA\Property(property: 'type', type: 'string', enum: ['success', 'error', 'warning', 'info'], example: 'success')]
    public string $type;

    #[OA\Property(property: 'toast', type: 'boolean', example: false)]
    public bool $toast;

    #[OA\Property(property: 'message', type: 'string', nullable: true, example: 'Operation successful.')]
    public ?string $message;
}
