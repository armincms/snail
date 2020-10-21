<?php

namespace Armincms\Snail\Properties; 

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use Armincms\Snail\Contracts\AsObject;
use Armincms\Snail\Contracts\Resolvable;

class Collection extends Property implements AsObject
{         
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

        $this->displayUsing(function($value, $resource, $attribute) { 
            return $this->getProperties()->resolveForDisplay($value)->keyBy->name->map->getValue()->all();
        });
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
        parent::resolve($resource, $attribute); 

        $this->value = $this->getProperties()->resolve($this->value)->keyBy->name->map->getValue()->all();
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
