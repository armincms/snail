<?php

namespace Armincms\Snail\Properties; 

use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use Armincms\Snail\Contracts\AsArray; 
 
class Map extends Property implements AsArray 
{         
    use ResolvesProperties;

    /**
     * The callback to be used to serialize the property's value.
     *
     * @var \Closure
     */
    public $using;  

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

        $this->using(Text::class); 

        $this->nullValues(function($values) {
            return empty($values);
        });
    }  

    /**
     * Init the using field.
     * 
     * @param  string $attribute 
     * @param  mixed $value     
     * @param  mixed $resource  
     * @return \Armincms\Snail\Properties\Property            
     */
    protected function prepareUsing($attribute, $value, $resource)
    {
        return call_user_func($this->using, $attribute, $value, $resource);
    }
    
    /**
     * Give the property for map.
     * 
     * @param  string|callable $using 
     * @return $this        
     */
    public function using($using)
    { 
        $this->using = is_callable($using) ? $using : function($attribute, $value, $resource) use ($using) {
            return $using::make($attribute);
        }; 

        return $this;
    }   

    /**
     * Preaparing for json shema.
     * 
     * @return [type] [description]
     */
    public function jsonSchema()
    { 
        $itemKey = $this->singularName();

        return array_merge(parent::jsonSchema(), [ 
            'items'   => "array[{$itemKey}]", 
            $itemKey => with(call_user_func($this->using, $this->attribute, [], []), function($property) {
                return $property->jsonSchema();
            })
        ]);
    }

    /**
     * Gue
     * @return [type] [description]
     */
    protected function singularName()
    {
        return Str::camel(Str::singular($this->name));
    } 
}
