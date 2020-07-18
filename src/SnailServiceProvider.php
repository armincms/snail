<?php

namespace Armincms\Snail;
 
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider; 

class SnailServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
 
        $this->registerResources();   
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/Console/stubs/SnailServiceProvider.stub' => base_path('App/Providers/SnailServiceProvider.php'),
        ], 'snail-provider'); 

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'snail-migrations');
    } 

    /**
     * Register the package resources such as routes, templates, etc.
     *
     * @return void
     */
    protected function registerResources()
    {  
        $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        \Route::group($this->routeConfiguration(), __DIR__.'/../routes/snail.php');
    }

    /**
     * Get the Snail route group configuration array.
     *
     * @return array
     */
    protected function routeConfiguration()
    {
        return [
            'middleware' => config('snail.middleware', null),
            'namespace'  => 'Armincms\Snail\Http\Controllers',
            'domain'     => config('snail.domain', null),
            'prefix'     => Snail::path(),
            'as'         => 'snail.',
        ];
    } 

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([ 
            // Console\BaseResourceCommand::class, 
            // Console\FieldCommand::class,
            // Console\InstallCommand::class, 
            // Console\PublishCommand::class,
            // Console\ResourceCommand::class,  
            // Console\UserCommand::class, 
        ]);
    } 
}
