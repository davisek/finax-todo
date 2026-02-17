<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Modules\General\Http\Resources\AppResponse;
use Modules\User\Http\Resources\AuthResource;
use Modules\User\Http\Requests\RegisterRequest;
use Modules\User\Services\Interfaces\IAuthService;
use Modules\User\Services\Interfaces\IUserService;

class RegisterController extends Controller
{
    private readonly IUserService $userService;
    private readonly IAuthService $authService;

    public function __construct(IUserService $userService, IAuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

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
