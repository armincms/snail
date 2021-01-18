<?php

namespace Armincms\Snail\Contracts;

use Illuminate\Http\Request;

interface Ordering
{
    /**
     * Get the column for the ordering.
     *
     * @return string
     */
    public function column();

    /**
     * Apply the ordering to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $direction); 
}
