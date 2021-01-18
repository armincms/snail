<?php

namespace Armincms\Snail;

use Illuminate\Http\Request;
use Armincms\Snail\Http\Requests\SnailRequest;

trait ResolvesOrderings
{
    /**
     * Get the orderings that are available for the given request.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function availableOrderings(SnailRequest $request)
    {
        return $this->resolveOrderings($request)->values();
    }

    /**
     * Get the orderings for the given request.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveOrderings(SnailRequest $request)
    {
        return collect(array_values($this->filter($this->orderings($request))));
    }

    /**
     * Get the orderings available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function orderings(Request $request)
    {
        return [];
    }
}
