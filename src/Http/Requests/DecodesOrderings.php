<?php

namespace Armincms\Snail\Http\Requests;

use Armincms\Snail\OrderingDecoder;

trait DecodesOrderings
{
    /**
     * Get the orderings for the request.
     *
     * @return array
     */
    public function orderings()
    {
        return (new OrderingDecoder($this->orderings, $this->availableOrderings()))->orderings();
    }

    /**
     * Get all of the possibly available orderings for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function availableOrderings()
    {
        return $this->newResource()->availableOrderings($this);
    }
}
