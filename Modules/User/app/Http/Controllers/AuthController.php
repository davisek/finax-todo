<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\General\Http\Resources\AppResponse;
use Modules\User\Http\Resources\AuthResource;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Get(
        path: '/api/v1/auth/me',
        summary: 'Get current user',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/AppResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/AuthResource'),
                        ], type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function show()
    {
        return AppResponse::success(
            data: AuthResource::from(Auth::user())
        );
    }
}
