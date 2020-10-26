<?php  

namespace Armincms\Snail\Properties; 
 
use Illuminate\Http\Request; 
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable; 
use Armincms\Snail\Http\Requests\SnailRequest;
use Armincms\Snail\Contracts\Resolvable;
use Armincms\Snail\Contracts\AsString;
use Armincms\Snail\Displayable;

abstract class Property extends Displayable implements Resolvable, AsString
{  
    use Macroable; 

    /**
     * The displayable name of the field.
     *
     * @var string
     */
    public $name;

    /**
     * The attribute name of the property.
     *
     * @var string
     */
    public $attribute;

    /**
     * The property's resolved value.
     *
     * @var mixed
     */
    public $value;

    /**
     * The callback to be used to resolve the property's display value.
     *
     * @var \Closure
     */
    public $displayCallback;

    /**
     * The callback to be used to resolve the property's value.
     *
     * @var \Closure
     */
    public $resolveCallback; 

    /**
     * Indicates if the field should be sortable.
     *
     * @var bool
     */
    public $sortable = false;

    /**
     * Indicates if the field is nullable.
     *
     * @var bool
     */
    public $nullable = false;

    /**
     * Values which will be replaced to null.
     *
     * @var array
     */
    public $nullValues = ['']; 

    protected $casts = [
        \Armincms\Snail\Contracts\AsBoolean::class => 'boolean',
        \Armincms\Snail\Contracts\AsInteger::class => 'integer',
        \Armincms\Snail\Contracts\AsNumber::class  => 'number',
        \Armincms\Snail\Contracts\AsArray::class   => 'array', 
        \Armincms\Snail\Contracts\AsObject::class  => 'object',
        \Armincms\Snail\Contracts\AsString::class  => 'string',  
    ];

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
        $this->name = $name;
        $this->resolveCallback = $resolveCallback;
        $this->attribute = $attribute ?? str_replace(' ', '_', Str::lower($name));
    } 

    /**
     * Resolve the property's value for display.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolveForDisplay($resource, $attribute = null)
    {
        $this->resource = $resource;

        $attribute = $attribute ?? $this->attribute; 
        
        if (! $this->displayCallback) {
            $this->resolve($resource, $attribute);
        } elseif (is_callable($this->displayCallback)) {
            tap($this->value ?? $this->resolveAttribute($resource, $attribute), function ($value) use ($resource, $attribute) {
                $this->value = call_user_func($this->displayCallback, $value, $resource, $attribute);
            });
        }
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
        $this->resource = $resource;

        $attribute = $attribute ?? $this->attribute; 

        if (! $this->resolveCallback) {
            $this->value = $this->resolveAttribute($resource, $attribute);
        } elseif (is_callable($this->resolveCallback)) {
            tap($this->resolveAttribute($resource, $attribute), function ($value) use ($resource, $attribute) {
                $this->value = call_user_func($this->resolveCallback, $value, $resource, $attribute);
            });
        }
    }

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param  mixed  $resource
     * @param  string  $attribute
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute)
    {
        return $attribute instanceof \Closure
                    ? call_user_func($attribute, $resource) 
                    : data_get($resource, str_replace('->', '.', $attribute));
    }

    /**
     * Define the callback that should be used to display the property's value.
     *
     * @param  callable  $displayCallback
     * @return $this
     */
    public function displayUsing(callable $displayCallback)
    {
        $this->displayCallback = $displayCallback;

        return $this;
    }

    /**
     * Define the callback that should be used to resolve the property's value.
     *
     * @param  callable  $resolveCallback
     * @return $this
     */
    public function resolveUsing(callable $resolveCallback)
    {
        $this->resolveCallback = $resolveCallback;

        return $this;
    }   

    /**
     * Indicate that the field should be nullable.
     *
     * @param  bool  $nullable
     * @param  array|Closure  $values
     * @return $this
     */
    public function nullable($nullable = true, $values = null)
    {
        $this->nullable = $nullable;

        if ($values !== null) {
            $this->nullValues($values);
        }

        return $this;
    }

    /**
     * Specify nullable values.
     *
     * @param  array|Closure  $values
     * @return $this
     */
    public function nullValues($values)
    {
        $this->nullValues = $values;

        return $this;
    }

    /**
     * Get the value of property.
     * 
     * @return mixed
     */
    public function getValue()
    {  
        return $this->isNullValue($this->value) 
                    ? null 
                    : $this->castValueAs($this->value, $this->getValueType());
    }  

    /**
     * Check value for null value.
     *
     * @param  mixed $value
     * @return bool
     */
    public function isNullValue($value)
    {
        if (! $this->nullable) {
            return false;
        }

        return is_callable($this->nullValues)
            ? ($this->nullValues)($value)
            : in_array($value, (array) $this->nullValues);
    }

    /**
     * Get the type of property value.
     * 
     * @return string
     */
    public function getValueType()
    {
        return collect($this->casts)->first(function($value, $key) {
            return $this instanceof $key;
        }, 'integer');
    }

    /**
     * Cast a value to specific type.
     * 
     * @param  mixed $value 
     * @param  string $type  
     * @return mixed        
     */
    protected function castValueAs($value, $type)
    {
        switch ($type) {
            case 'integer':
                return intval($value);
                break;

            case 'number':
            case 'float':
            case 'double':
                return floatval($value);
                break;

            case 'object': 
                return (object) Collection::make($value)->jsonSerialize();
                break;

            case 'array': 
                return (array) Collection::make($value)->values()->jsonSerialize();
                break;

            case 'boolean': 
            case 'bool': 
                return boolval($value);
                break;
            
            default:
                return strval($value);
                break;
        } 
    } 

    /**
     * Preaparing for json shema.
     * 
     * @return [type] [description]
     */
    public function jsonSchema()
    {
        return array_merge([ 
            'type'  => $this->getValueType(),
        ]);
    }

    /**
     * Prepare the property for client schema consumption.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function serializeForSchema(Request $request)
    {
        return array_merge($this->jsonSchema(), [
            'name'         => $this->name,
            'showOnDetail' => $this->showOnDetail,
            'showOnIndex'  => $this->showOnIndex,
            'nullable'     => $this->nullable, 
            'default'      => $this->isNullValue($this->value) ? null : $this->value, 
            'nullValues'   => ! $this->nullable || is_callable($this->nullValues) 
                                    ? [] : (array) $this->nullValues,
        ]);
    } 

    /**
     * Prepare the property for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    { 
        return with(app(SnailRequest::class), function ($request) {
            return array_merge([
                'name'     => $this->name,   
                'nullable' => $this->nullable,   
                'sortable' => $this->sortable, 
                'value'    => $this->getValue(),
                'type'     => $this->getValueType(),
                // 'sortableUriKey' => $this->sortableUriKey(),  
            ], $this->meta());
        });
    } 
}