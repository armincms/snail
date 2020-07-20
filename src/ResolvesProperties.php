<?php

namespace Armincms\Snail;

use Closure;  
use Armincms\Snail\Contracts\ListableProperty;
use Armincms\Snail\Contracts\Resolvable; 
use Armincms\Snail\Properties\PropertyCollection;  
use Armincms\Snail\Http\Requests\SnailRequest;

trait ResolvesProperties
{
    /**
     * Resolve the index properties.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    public function indexProperties(SnailRequest $request)
    {
        return $this
                ->availableProperties($request) 
                ->filterForIndex($request, $this->resource)
                // ->withoutListableProperties()
                ->authorized($request)
                ->resolveForDisplay($this->resource);
    }

    /**
     * Resolve the detail properties.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    public function detailProperties(SnailRequest $request)
    {
        return $this
                ->availableProperties($request) 
                ->filterForDetail($request, $this->resource)
                ->authorized($request)
                ->resolveForDisplay($this->resource);
    } 

    /**
     * Resolve the given properties to their values.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Closure|null  $filter
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    protected function resolveProperties(SnailRequest $request, Closure $filter = null)
    {
        $properties = $this->resolveNonPivotProperties($request); 

        return is_null($filter) ? $properties : $filter($properties); 
    }

    /**
     * Resolve the non pivot properties for the resource.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    protected function resolveNonPivotProperties(SnailRequest $request)
    {
        return $this->availableProperties($request)
            ->resolve($this->resource)
            ->authorized($request);
    }

    protected function resolvePropertiesForDetail(SnailRequest $request, Closure $filter)
    {
        return $this->resolveNonPivotProperties($request); 
    }

    /**
     * Resolve the property for the given attribute.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  string  $attribute
     * @return \Armincms\Snail\Properties\Property
     */
    public function resolvePropertyForAttribute(SnailRequest $request, $attribute)
    {
        return $this->resolveProperties($request)->findPropertyByAttribute($attribute);
    } 

    /**
     * Get the properties that are available for the given request.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    public function availableProperties(SnailRequest $request)
    {
        $method = $this->propertiesMethod($request);

        return PropertyCollection::make(array_values($this->filter($this->{$method}($request))));
    }

    /**
     * Compute the method to use to get the available properties.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return string
     */
    protected function propertiesMethod(SnailRequest $request)
    {
        if ($request->isIndexRequest() && method_exists($this, 'propertiesForIndex')) {
            return 'propertiesForIndex';
        }

        if ($request->isDetailRequest() && method_exists($this, 'propertiesForDetail')) {
            return 'propertiesForDetail';
        } 

        return 'properties';
    }
}
