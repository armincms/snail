<?php

namespace Armincms\Snail\Http\Middleware;

use Armincms\Snail\Events\ServingSnail;

class DispatchServingSnailEvent
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
        event(new ServingSnail($request));

        return $next($request);
    }
}
