<?php

namespace Armincms\Snail\Properties; 

use Armincms\Snail\Contracts\AsInteger; 

class ID extends Property  implements AsInteger
{       
    /**
     * Create a new field.
     *
     * @param  string|null  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name = null, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name ?? 'ID', $attribute, $resolveCallback);
    }
}
