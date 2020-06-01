<?php

namespace Armincms\Snail\Http\Requests;

use Armincms\Snail\FilterDecoder;

trait DecodesFilters
{
    /**
     * Get the filters for the request.
     *
     * @return array
     */
    public function filters()
    {
        return (new FilterDecoder($this->filters, $this->availableFilters()))->filters();
    }

    /**
     * Get all of the possibly available filters for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function availableFilters()
    {
        return $this->newResource()->availableFilters($this);
    }
}
