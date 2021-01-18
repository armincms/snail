<?php

namespace Armincms\Snail\Query;

use Illuminate\Http\Request;

class ApplyOrdering
{
    /**
     * The ordering instance.
     *
     * @var \Armincms\Snail\Orderings\Ordering
     */
    public $ordering;

    /**
     * The direction of the ordering.
     *
     * @var mixed
     */
    public $direction;

    /**
     * Create a new invokable ordering applier.
     *
     * @param  \Armincms\Snail\Orderings\Ordering  $ordering
     * @param  mixed  $direction
     * @return void
     */
    public function __construct($ordering, $direction)
    {
        $this->direction = $direction;
        $this->ordering = $ordering;
    }

    /**
     * Apply the ordering to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Request $request, $query)
    {
        $this->ordering->apply(
            $request, $query, $this->direction
        );

        return $query;
    }
}
