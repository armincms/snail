<?php

namespace Armincms\Snail\Properties; 

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use Armincms\Snail\Contracts\AsObject;
use Armincms\Snail\Contracts\Resolvable;

class Collection extends Property implements AsObject
{         
    use ResolvesProperties;

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
     * Resolve the property's for hte given resolver.
     *
     * @param  mixed  $resource
     * @param  callable  $resolve
     * @return array
     */
    protected function resolveVia($resource, $resolve)
    {
        return $this->getProperties()
                    ->$resolve($this->value) 
                    ->keyBy->name 
                    ->map->getValue() 
                    ->all();  
    }

    public function getProperties()
    {
        return PropertyCollection::make(call_user_func($this->propertiesCallback));
    }

    public function properties(callable $properties)
    {
        $this->propertiesCallback = $properties;

        return $this;
    }

    /**
     * Get the type of property value.
     * 
     * @return string
     */
    // protected function getValueType()
    // {
    //     return Str::camel(Str::singular($this->name).'-'.parent::getValueType());
    // } 

    /**
     * Preaparing for json shema.
     * 
     * @return [type] [description]
     */
    public function jsonSchema()
    {  
        return array_merge(parent::jsonSchema(), [  
            'properties' => $this->getProperties()->map(function($property) {
                return array_merge($property->jsonSchema(), [
                    'name' => $property->name,
                ]);
            }),
        ]);
    } 
}
