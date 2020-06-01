<?php

namespace Armincms\Snail\Http\Requests;

class ResourceIndexRequest extends ResourceRequest
{
    use /*CountsResources,*/ QueriesResources;

    /**
     * Get the count of the resources.
     *
     * @return int
     */
    public function toCount()
    {
        return $this->buildCountQuery($this->toQuery())->count();
    }
}
