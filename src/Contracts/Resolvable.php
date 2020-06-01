<?php

namespace Armincms\Snail\Contracts;

interface Resolvable
{
    /**
     * Resolve the property's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null);
}
