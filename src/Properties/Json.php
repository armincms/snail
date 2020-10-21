<?php

namespace Armincms\Snail\Properties; 

use Closure;
use Armincms\Snail\Contracts\AsObject;
use Armincms\Snail\Contracts\Resolvable;

class Json extends Property implements AsObject
{        
    use BehavesAsArray;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  callable|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->propertiesCallback = function() {
            return [ 
            ];
        };
    }

    /**
     * Resolve the property's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    { 
        $items = collect(call_user_func($this->propertiesCallback))
                    ->keyBy($attribute)
                    ->each(function($property, $attribute) use ($resource) {
                        $property->resolve($resource, "{$this->attribute}->{$attribute}"); 
                    })
                    ->map->getValue();

        $this->value = $this->asArray ? $items->values()->all() : $items->all();
    }  

    public function properties(Closure $properties)
    {
        $this->propertiesCallback = $properties;

        return $this;
    }
}
