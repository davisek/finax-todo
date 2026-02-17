<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\General\Http\Resources\AppResponse;
use OpenApi\Attributes as OA;

class LogoutController extends Controller
{
    #[OA\Post(
        path: '/api/v1/auth/logout',
        summary: 'Logout user',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/AppResponse'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout()
    {
        $user = Auth::user();
        $sessionId = null;

        if ($current = $user?->currentAccessToken()) {
            $sessionId = collect($current->abilities)
                ->first(fn($a) => str_starts_with($a, 'session:'));
        } elseif ($plain = request()->cookie('refresh_token')) {
            if ($pat = PersonalAccessToken::findToken($plain)) {
                $sessionId = collect($pat->abilities)
                    ->first(fn($a) => str_starts_with($a, 'session:'));
            }
        }

        if ($user && $sessionId) {
            $user->tokens()
                ->whereJsonContains('abilities', $sessionId)
                ->delete();
        }

        $forget = cookie(
            name: 'refresh_token',
            value: '',
            minutes: -1,
            path: config('user.refresh_cookie_path'),
            domain: config('user.refresh_cookie_domain'),
            secure: (bool) config('user.refresh_cookie_secure'),
            httpOnly: true,
            sameSite: config('user.refresh_cookie_samesite')
        );

        return AppResponse::successToast(
            Lang::get('user::success.LOGOUT_SUCCESSFUL')
        )->addCookie($forget);
    }
}
