<?php

namespace Armincms\Snail\Filters;

use JsonSerializable;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Armincms\Snail\Snail;
use Armincms\Snail\Metable;  
use Armincms\Snail\Contracts\Filter as FilterContract;

abstract class Filter implements FilterContract, JsonSerializable
{
    use Metable;  

    /**
     * The displayable name of the filter.
     *
     * @var string
     */
    public $name; 

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function apply(Request $request, $query, $value);

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    abstract public function options(Request $request); 

    /**
     * Get the displayable name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Snail::humanize($this);
    }

    /**
     * Get the key for the filter.
     *
     * @return string
     */
    public function key()
    {
        return Str::snake(class_basename($this));
    }

    /**
     * Set the default options for the filter.
     *
     * @return array|mixed
     */
    public function default()
    {
        return '';
    }

    /**
     * Prepare the filter for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $container = Container::getInstance();

        return array_merge([
            'key' => $this->key(), 
            'name' => $this->name(), 
            'options' => collect($this->options($container->make(Request::class)))->map(function ($value, $key) {
                return is_array($value) ? ($value + ['value' => $key]) : ['name' => $key, 'value' => $value];
            })->values()->all(),
            'currentValue' => $this->default() ?? '',
        ], $this->meta());
    }
}
