<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Modules\General\Http\Resources\AppResponse;
use Modules\User\Http\Resources\AuthResource;
use Modules\User\Http\Requests\RegisterRequest;
use Modules\User\Services\Interfaces\IAuthService;
use Modules\User\Services\Interfaces\IUserService;
use OpenApi\Attributes as OA;

class RegisterController extends Controller
{
    public function __construct(
        private readonly IUserService $userService,
        private readonly IAuthService $authService,
    ) {}

    #[OA\Post(
        path: '/api/v1/auth/register',
        summary: 'Register user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Test User'),
                    new OA\Property(property: 'email', type: 'string', example: 'test@gmail.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'Password1!'),
                    new OA\Property(property: 'password_confirmation', type: 'string', example: 'Password1!'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/AppResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'auth', ref: '#/components/schemas/AuthResponse'),
                        ], type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function register(RegisterRequest $request)
    {
        $user = $this->userService->create($request);
        $tokens = $this->authService->createTokenPair($user);
        $refreshMinutes = (int) config('user.refresh_token_ttl', 7) * 24 * 60;

        $cookie = cookie(
            name: 'refresh_token',
            value: $tokens['refresh_token'],
            minutes: $refreshMinutes,
            path: config('user.refresh_cookie_path'),
            domain: config('user.refresh_cookie_domain'),
            secure: config('user.refresh_cookie_secure'),
            httpOnly: true,
            sameSite: config('user.refresh_cookie_samesite')
        );

        return AppResponse::successToast(
            message: Lang::get('user::success.REGISTERED_SUCCESSFULLY'),
            auth: [
                'user' => AuthResource::from($user),
                'access_token' => $tokens['access_token'],
                'expires_in' => $tokens['expires_in'],
            ]
        )->addCookie($cookie);
    }
}
