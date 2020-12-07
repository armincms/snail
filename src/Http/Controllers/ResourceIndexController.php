<?php

namespace Armincms\Snail\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
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
        $paginator = $this->paginator($request, $resource = $request->newResource());  
 
        return response()->json(array_merge(
            (array) $resource::additionalInformation($request), 
            [
                'links' => $this->paginationLinks($paginator->toArray()),
                'meta' => $this->meta($paginator->toArray())
            ],
            [
                'label' => $resource::label(),  
                'singularLabel' => $resource::singularLabel(),  
                'data' => $paginator->getCollection()->mapInto($resource)->map->serializeForIndex($request),
            ]
        ), 200, [], JSON_PRESERVE_ZERO_FRACTION);
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
        return $request->toQuery()->paginate(
            $request->perPage ?? $resource::perPageOptions()[0]
        );
    }
    /**
     * Get the pagination links for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function paginationLinks($paginated)
    {
        return [
            'first' => $paginated['first_page_url'] ?? null,
            'last' => $paginated['last_page_url'] ?? null,
            'prev' => $paginated['prev_page_url'] ?? null,
            'next' => $paginated['next_page_url'] ?? null,
        ];
    }

    /**
     * Gather the meta data for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function meta($paginated)
    {
        return Arr::except($paginated, [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]);
    }
}
