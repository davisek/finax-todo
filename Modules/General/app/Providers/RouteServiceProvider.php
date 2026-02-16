<?php

namespace Modules\General\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'General';

    public function boot(): void
    {
        parent::boot();
    }
}
