<?php
 
use Armincms\Snail\Http\Middleware\DispatchServingSnailEvent;

return [

    /*
    |--------------------------------------------------------------------------
    | Snail App Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to display the name of the application within the UI
    | or in other locations. Of course, you're free to change the value.
    |
    */

    'name' => env('SNAIL_APP_NAME', env('APP_NAME')),

    /*
    |--------------------------------------------------------------------------
    | Snail Domain Name
    |--------------------------------------------------------------------------
    |
    | This value is the domain name associated with your application. This
    | can be used to prevent Snail's internal routes from being registered
    | on subdomains which do not need access to your admin application.
    |
    */

    'domain' => env('SNAIL_DOMAIN_NAME', null), 

    /*
    |--------------------------------------------------------------------------
    | Snail Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Snail will be accessible from. Feel free to
    | change this path to anything you like. Note that this URI will not
    | affect Snail's internal API routes which aren't exposed to users.
    |
    */

    'path' => '/snail',

    /*
    |--------------------------------------------------------------------------
    | Snail Authentication Guard
    |--------------------------------------------------------------------------
    |
    | This configuration option defines the authentication guard that will
    | be used to protect your Snail routes. This option should match one
    | of the authentication guards defined in the "auth" config file.
    |
    */

    'guard' => env('SNAIL_GUARD', null), 

    /*
    |--------------------------------------------------------------------------
    | Snail Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be assigned to every Snail route, giving you the
    | chance to add your own middleware to this stack or override any of
    | the existing middleware. Or, you can just stick with this stack.
    |
    */

    'middleware' => [
        // 'web', should use santcum 
        DispatchServingSnailEvent::class, 
    ],

    /*
    |--------------------------------------------------------------------------
    | Snail Pagination Type
    |--------------------------------------------------------------------------
    |
    | This option defines the visual style used in Snail's resource pagination.
    | You may choose between 3 types: "simple", "load-more" and "links".
    | Feel free to set this option to the visual style you like.
    |
    */

    'pagination' => 'simple', 

];
