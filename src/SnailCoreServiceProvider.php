<?php

namespace Armincms\Snail;
 
use Illuminate\Support\ServiceProvider; 
use Armincms\Snail\Http\Middleware\ServeSnail;
use Illuminate\Contracts\Http\Kernel as HttpKernel;

/**
 * The primary purpose of this service provider is to push the ServeSnail
 * middleware onto the middleware stack so we only need to register a
 * minimum number of resources for all other incoming app requests.
 */
class SnailCoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    { 
        if ($this->app->runningInConsole()) {
            $this->app->register(SnailServiceProvider::class);
        } 

        $this->mergeConfigFrom(__DIR__.'/../config/snail.php', 'snail'); 

        $this->app->make(HttpKernel::class)
                    ->pushMiddleware(ServeSnail::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! defined('SNAIL_PATH')) {
            define('SNAIL_PATH', realpath(__DIR__.'/../'));
        } 
    }
}
