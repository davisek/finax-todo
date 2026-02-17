<?php

namespace Modules\General\Http\Middlewares;

use Closure;

class AcceptJsonMiddleware
{
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
