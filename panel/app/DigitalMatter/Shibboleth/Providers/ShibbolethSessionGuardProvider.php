<?php
namespace App\DigitalMatter\Shibboleth\Providers;

use Illuminate\Support\ServiceProvider;
use App\DigitalMatter\Shibboleth\Auth\ShibbolethSessionGuard;

class ShibbolethSessionGuardProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend(
            'shibboleth',
            function ($app) {
                $provider = new EloquentUserProvider($app['hash'], config('auth.providers.users.model'));
                return new ShibbolethSessionGuard('shibboleth', $provider, app()->make('session.store'), request());
            }
            );
    }
}

