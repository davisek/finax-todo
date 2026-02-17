<?php

namespace Modules\User\Services\Interfaces;

use Modules\User\Http\Requests\RegisterRequest;
use Modules\User\Models\User;

interface IUserService
{
    public function create(RegisterRequest $request): User;
}
