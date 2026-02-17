<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Modules\General\Http\Resources\AppResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $debug = (bool) config('app.debug');

        RateLimiter::for('api-global', function (Request $r) use ($debug) {
            if ($debug) {
                return Limit::perMinute(100)->by($r->ip());
            }

            return $this->limitWithResponse(
                Limit::perMinute(60)->by($r->ip())
            );
        });

        RateLimiter::for('auth-register', function (Request $r) use ($debug) {
            $key = $r->user()?->id ?? $r->ip();

            if ($debug) {
                return Limit::perMinute(100)->by($key);
            }

            return $this->limitWithResponse(
                Limit::perMinute(3)->by($key)
            );
        });

        RateLimiter::for('auth-login', function (Request $r) use ($debug) {
            $email = strtolower(trim((string) $r->input('email')));
            $key = $email !== '' ? "login:{$email}:{$r->ip()}" : "login-ip:{$r->ip()}";

            if ($debug) {
                return Limit::perMinute(100)->by($key);
            }

            return $this->limitWithResponse(
                Limit::perMinute(3)->by($key)
            );
        });

        RateLimiter::for('auth-refresh', function (Request $r) use ($debug) {
            $key = $r->user()?->id ?? $r->ip();

            if ($debug) {
                return Limit::perMinute(100)->by($key);
            }

            return $this->limitWithResponse(
                Limit::perMinute(10)->by($key)
            );
        });

        RateLimiter::for('auth-revoke', function (Request $r) use ($debug) {
            $key = $r->user()?->id ?? $r->ip();

            if ($debug) {
                return Limit::perMinute(100)->by($key);
            }

            return $this->limitWithResponse(
                Limit::perMinute(10)->by($key)
            );
        });

        RateLimiter::for('auth-logout', function (Request $r) use ($debug) {
            $key = $r->user()?->id ?? $r->ip();

            if ($debug) {
                return Limit::perMinute(100)->by($key);
            }

            return $this->limitWithResponse(
                Limit::perMinute(10)->by($key)
            );
        });
    }

    private function limitWithResponse(Limit $limit): Limit
    {
        return $limit->response(function (Request $request, array $headers) {
            $resource = AppResponse::errorToast(
                Lang::get('user::error.THROTTLE', [
                    'seconds' => $headers['Retry-After'] ?? null,
                ])
            );

            $response = $resource->toResponse($request);
            $response->setStatusCode(429);

            foreach ($headers as $name => $value) {
                $response->headers->set($name, $value);
            }

            return $response;
        });
    }
}
