<?php

namespace Armincms\Snail\Http\Middleware;

use Armincms\Snail\Snail;

class Authorize
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
        return Snail::check($request) ? $next($request) : abort(403);
    }
}
