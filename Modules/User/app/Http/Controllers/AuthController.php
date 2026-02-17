<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\General\Http\Resources\AppResponse;
use Modules\User\Http\Resources\AuthResource;

class AuthController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return AppResponse::success(
            data: AuthResource::from($user)
        );
    }
}
