<?php

namespace Armincms\Snail\Orderings;

use Illuminate\Http\Request;

class CreatedAt extends Ordering
{   
    /**
     * Apply the order to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $direction)
    {
        $ordering = $direction == 'asc' ? 'latest' : 'oldest';

        return $query->{$ordering}($this->column());
    } 
}
