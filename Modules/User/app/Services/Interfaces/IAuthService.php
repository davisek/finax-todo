<?php

namespace Modules\User\Services\Interfaces;

use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Models\User;

interface IAuthService
{
    public function getUserByEmail(string $email): ?User;

    public function verifyCredentials(LoginRequest $request): User;

    public function createTokenPair(User $user): array;
}
