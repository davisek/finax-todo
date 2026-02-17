<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Modules\General\Http\Resources\AppResponse;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Http\Resources\AuthResource;
use Modules\User\Services\Interfaces\IAuthService;
use OpenApi\Attributes as OA;

class LoginController extends Controller
{
    public function __construct(
        private readonly IAuthService $authService,
    ) {}

    #[OA\Post(
        path: '/api/v1/auth/login',
        summary: 'Login user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'test@gmail.com'),
                    new OA\Property(property: 'password', type: 'string', example: '12345678'),
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
            new OA\Response(response: 401, description: 'Invalid credentials'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function login(LoginRequest $request)
    {
        $user = $this->authService->verifyCredentials($request);
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
            Lang::get('user::success.LOGIN_SUCCESSFUL'),
            auth: [
                'user' => AuthResource::from($user),
                'access_token' => $tokens['access_token'],
                'expires_in' => $tokens['expires_in'],
            ]
        )->addCookie($cookie);
    }
}
