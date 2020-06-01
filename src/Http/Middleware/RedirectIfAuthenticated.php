<?php

namespace Armincms\Snail\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Armincms\Snail\Snail;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect(Snail::path());
        }

        return $next($request);
    }
}
