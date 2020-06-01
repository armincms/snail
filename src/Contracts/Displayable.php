<?php

namespace Armincms\Snail\Contracts;

use Armincms\Snail\Http\Requests\SnailRequest;

interface Displayable
{  
    /**
     * Determine if the object can display.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  mixed  $schema
     * @return bool
     */
    public function canDisplay(SnailRequest $request, $schema);
}
