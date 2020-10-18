<?php

namespace Armincms\Snail\Http\Middleware;

use Armincms\Snail\Events\ServingSnail;
use Armincms\Snail\Snail;

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
        $this->prepareTheSnail($request); 

        event(new ServingSnail($request));

        return $next($request);
    }

    /**
     * Configuring the Snail before serving. 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void         
     */
    public function prepareTheSnail($request)
    {
        if($request->route()->hasParameter('version')) {
            Snail::setVersion($request->route('version'));
        } else {
            Snail::setDefaultVersion();
        } 
    }
}
