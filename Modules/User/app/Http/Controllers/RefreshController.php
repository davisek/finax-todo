<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\General\Http\Resources\AppResponse;
use Modules\User\Http\Resources\AuthResource;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class RefreshController extends Controller
{
    public function refresh()
    {
        $refreshToken = request()->cookie('refresh_token');

        if (!$refreshToken) {
            throw new UnauthorizedHttpException('Bearer', Lang::get('user::error.NO_REFRESH_TOKEN'));
        }

        $token = PersonalAccessToken::findToken($refreshToken);

        if (!$token || $token->name !== 'refresh_token') {
            throw new UnauthorizedHttpException('Bearer', Lang::get('user::error.INVALID_REFRESH_TOKEN'));
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            $token->delete();
            throw new UnauthorizedHttpException('Bearer', Lang::get('user::error.REFRESH_TOKEN_EXPIRED'));
        }

        $user = $token->tokenable;

        $accessTokenTtl = (int) config('user.access_token_ttl', 15);
        $refreshTokenTtl = (int) config('user.refresh_token_ttl', 7);

        $sessionId = collect($token->abilities)->first(fn($a) => str_starts_with($a, 'session:'))
            ?: 'session:' . Str::uuid();

        $user->tokens()
            ->where('name', 'access_token')
            ->whereJsonContains('abilities', $sessionId)
            ->delete();

        $newAccessToken = $user
            ->createToken('access_token', [$sessionId], now()->addMinutes($accessTokenTtl))
            ->plainTextToken;

        if (config('user.rotate_refresh_token')) {
            $token->delete();

            $newRefreshToken = $user
                ->createToken('refresh_token', [$sessionId], now()->addDays($refreshTokenTtl))
                ->plainTextToken;

            $newCookie = cookie(
                name: 'refresh_token',
                value: $newRefreshToken,
                minutes: $refreshTokenTtl * 24 * 60,
                path: config('user.refresh_cookie_path'),
                domain: config('user.refresh_cookie_domain'),
                secure: config('user.refresh_cookie_secure'),
                httpOnly: true,
                sameSite: config('user.refresh_cookie_samesite')
            );

            return AppResponse::success(
                message: Lang::get('user::success.TOKEN_REFRESHED'),
                auth: [
                    'access_token' => $newAccessToken,
                    'expires_in' => $accessTokenTtl * 60,
                ]
            )->addCookie($newCookie);
        }

        return AppResponse::success(
            message: Lang::get('user::success.TOKEN_REFRESHED'),
            auth: [
                'access_token' => $newAccessToken,
                'expires_in' => $accessTokenTtl * 60,
            ]
        );
    }

    public function revoke()
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException(Lang::get('user::error.UNAUTHENTICATED'));
        }

        $user->tokens()->delete();

        return AppResponse::success(
            message: Lang::get('user::success.REFRESH_TOKENS_REVOKED')
        )->addCookie(cookie(
            name: 'refresh_token',
            value: '',
            minutes: -1,
            path: config('user.refresh_cookie_path'),
            domain: config('user.refresh_cookie_domain'),
            secure: config('user.refresh_cookie_secure'),
            httpOnly: true,
            sameSite: config('user.refresh_cookie_samesite')
        ));
    }

    public function check()
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException(Lang::get('user::error.INVALID_TOKEN'));
        }

        $token = $user->currentAccessToken();

        return AppResponse::success(
            message: Lang::get('user::success.TOKEN_VALID'),
            data: [
                'user' => AuthResource::from($user),
                'token_expires_at' => $token->expires_at,
            ]
        );
    }
}
