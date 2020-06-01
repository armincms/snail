<?php

namespace Armincms\Snail\Http\Controllers;

use Illuminate\Routing\Controller;
use Armincms\Snail\Http\Requests\ResourceIndexRequest; 
use Armincms\Snail\Http\Resources\ResourceCollection; 
use Illuminate\Support\Arr;

class ResourceIndexController extends Controller
{
    /**
     * List the resources for administration.
     *
     * @param  \Armincms\Snail\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(ResourceIndexRequest $request)
    {    
        $paginator = $this->paginator(
            $request, $resource = $request->resource()
        );  

        $additionals = (array) $resource::additionalInformation($request);

        return response()->json(array_merge($paginator->jsonSerialize(), $additionals, [
                'label' => $resource::label(),  
                'singularLabel' => $resource::singularLabel(),  
                'data' => $paginator->getCollection()
                                    ->mapInto($resource)
                                    ->map->serializeForIndex($request),
        ]));
    }

    /**
     * Get the paginator instance for the index request.
     *
     * @param  \Armincms\Snail\Http\Requests\ResourceIndexRequest  $request
     * @param  string  $resource
     * @return \Illuminate\Pagination\Paginator
     */
    protected function paginator(ResourceIndexRequest $request, $resource)
    {
        return $request->toQuery()->simplePaginate(
            $request->perPage ?? $resource::perPageOptions()[0]
        );
    }
}
