<?php

namespace Armincms\Snail\Properties;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Armincms\Snail\Contracts\Resolvable; 
use Armincms\Snail\Contracts\ListableProperty;
use Armincms\Snail\Http\Requests\SnailRequest;

class PropertyCollection extends Collection
{
    /**
     * Find a given property by its attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $default
     * @return \Armincms\Snail\Properties\Property|null
     */
    public function findPropertyByAttribute($attribute, $default = null)
    {
        return $this->first(function ($property) use ($attribute) {
            return isset($property->attribute) &&
                $property->attribute == $attribute;
        }, $default);
    }

    /**
     * Filter elements should be displayed for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    public function authorized(Request $request)
    {
        return $this->filter(function ($property) use ($request) {
            return $property->authorize($request);
        })->values();
    }

    /**
     * Filter elements should be displayed for the given request.
     *
     * @param  mixed  $resource
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    public function resolve($resource)
    {
        return $this->each(function ($property) use ($resource) {
            if ($property instanceof Resolvable) {
                $property->resolve($resource);
            }
        });
    }

    /**
     * Resolve value of propertys for display.
     *
     * @param  mixed  $resource
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    public function resolveForDisplay($resource)
    {
        return $this->each(function ($property) use ($resource) { 
            if ($property instanceof Resolvable) {
                $property->resolveForDisplay($resource);
            }
        });
    }

    /**
     * Filter propertys for showing on detail.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  mixed  $resource
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    public function filterForDetail(SnailRequest $request, $resource)
    {
        return $this->filter(function ($property) use ($resource, $request) {
            return $property->isShownOnDetail($request, $resource);
        })->values();
    }

    /**
     * Filter propertys for showing on index.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  mixed  $resource
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    public function filterForIndex(SnailRequest $request, $resource)
    {
        return $this->filter(function ($property) use ($resource, $request) {
            return $property->isShownOnIndex($request, $resource);
        })->values();
    } 

    /**
     * Reject propertys which use their own index listings.
     *
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    public function withoutListableProperties()
    {
        return $this->reject(function ($property) {
            return $property instanceof ListableProperty;
        });
    }

    /**
     * Filter the propertys to only many-to-many relationships.
     *
     * @return \Armincms\Snail\Properties\PropertyCollection
     */
    public function filterForManyToManyRelations()
    {
        return $this->filter(function ($property) {
            return $property instanceof BelongsToMany || $property instanceof MorphToMany;
        });
    }
}
