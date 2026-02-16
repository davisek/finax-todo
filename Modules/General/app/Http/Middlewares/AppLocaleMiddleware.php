<?php

namespace Modules\General\Http\Middlewares;

use Closure;
use Illuminate\Support\Facades\App;
use Modules\General\Classes\Enums\AppLocale;

class AppLocaleMiddleware
{
    public function handle($request, Closure $next)
    {
        $locale = $request->header('Accept-Language');

        if (!in_array($locale, AppLocale::values()))
            $locale = AppLocale::default()->value;

        App::setLocale($locale);

        return $next($request);
    }
}
