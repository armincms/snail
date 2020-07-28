<?php

namespace Armincms\Snail\Filters;

use Illuminate\Container\Container;
use Illuminate\Http\Request;

abstract class BooleanFilter extends Filter
{ 
    /**
     * Set the default options for the filter.
     *
     * @return array
     */
    public function default()
    {
        $container = Container::getInstance();

        return collect($this->options($container->make(Request::class)))->values()->mapWithKeys(function ($option) {
            return [$option => false];
        })->all();
    }
}
