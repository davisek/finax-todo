<?php

namespace Modules\Todo\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TodoBelongsToUser
{
    public function handle(Request $request, Closure $next)
    {
        $todo = $request->route('todo');

        if ($todo->user_id !== Auth::id()) {
            throw new NotFoundHttpException(
                Lang::get('todo::error.NOT_FOUND')
            );
        }

        return $next($request);
    }
}
