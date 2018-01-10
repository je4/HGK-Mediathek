<?php
namespace App\Providers\Digma;

use Session;
use Illuminate\Support\ServiceProvider;
use app\Extensions\Digma\DigmaSessionStore;

class DigmaSessionProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Session::extend('digma', function($app) {
            // Return implementation of SessionHandlerInterface...
            return new DigmaSessionStore;
        });
    }
    
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

