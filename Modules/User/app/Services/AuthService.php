<?php

namespace Modules\User\Services;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Models\User;
use Modules\User\Services\Interfaces\IAuthService;

class AuthService implements IAuthService
{
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function verifyCredentials(LoginRequest $request): User
    {
        $user = $this->getUserByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new AuthenticationException(
                Lang::get('user::error.INVALID_CREDENTIALS')
            );
        }

        return $user;
    }

    public function createTokenPair(User $user): array
    {
        $accessTokenTtl = (int) config('user.access_token_ttl', 15);
        $refreshTokenTtl = (int) config('user.refresh_token_ttl', 7);
        $sessionId = 'session:' . Str::uuid();

        $accessToken = $user
            ->createToken('access_token', [$sessionId], now()->addMinutes($accessTokenTtl))
            ->plainTextToken;

        $refreshToken = $user
            ->createToken('refresh_token', [$sessionId], now()->addDays($refreshTokenTtl))
            ->plainTextToken;

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $accessTokenTtl * 60,
        ];
    }
}
