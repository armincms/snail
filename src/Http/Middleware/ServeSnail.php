<?php

namespace Armincms\Snail\Http\Middleware;

use Armincms\Snail\Events\SnailServiceProviderRegistered;
use Armincms\Snail\SnailServiceProvider;
use Armincms\Snail\Snail;

class ServeSnail
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        if ($this->isSnailRequest($request)) {
            app()->register(SnailServiceProvider::class);
 
            event(new SnailServiceProviderRegistered); 
        }

        return $next($request);
    }

    /**
     * Determine if the given request is intended for Snail.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isSnailRequest($request)
    {
        $path = trim(Snail::path(), '/') ?: '/';

        return (app()->environment('local') || $request->expectsJson()) && 
               ($request->is($path) || $request->is(trim($path.'/*', '/')));
    }
}
