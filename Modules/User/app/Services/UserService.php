<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\RegisterRequest;
use Modules\User\Models\User;
use Modules\User\Services\Interfaces\IUserService;

class UserService implements IUserService
{
    public function create(RegisterRequest $request): User
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $user;
    }
}
