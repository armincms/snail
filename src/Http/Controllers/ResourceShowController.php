<?php

namespace Armincms\Snail\Http\Controllers;

use Illuminate\Routing\Controller;
use Armincms\Snail\Http\Requests\ResourceDetailRequest;

class ResourceShowController extends Controller
{
    /**
     * Display the resource for administration.
     *
     * @param  \Armincms\Snail\Http\Requests\ResourceDetailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(ResourceDetailRequest $request)
    {
        $resource = $request->newResourceWith(tap($request->findModelQuery(), function ($query) use ($request) {
            $request->newResource()->detailQuery($request, $query);
        })->firstOrFail());

        // $resource->authorizeToView($request);  

        return response()->json($resource->serializeForDetail($request)); 
    }
}
