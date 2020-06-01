<?php

namespace Armincms\Snail;

use Illuminate\Http\Request;
use Armincms\Snail\Http\Requests\SnailRequest;

trait ResolvesFilters
{
    /**
     * Get the filters that are available for the given request.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function availableFilters(SnailRequest $request)
    {
        return $this->resolveFilters($request)->filter->authorizedToSee($request)->values();
    }

    /**
     * Get the filters for the given request.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveFilters(SnailRequest $request)
    {
        return collect(array_values($this->filter($this->filters($request))));
    }

    /**
     * Get the filters available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }
}
