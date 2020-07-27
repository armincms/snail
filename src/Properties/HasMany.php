<?php

namespace Armincms\Snail\Properties;
   
use Illuminate\Http\Request; 
use Armincms\Snail\Contracts\ListableProperty;
use Armincms\Snail\Contracts\AsArray;

class HasMany extends Relation implements ListableProperty, AsArray
{        
    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $value = null;

        if ($resource->relationLoaded($this->attribute)) {
            $value = $resource->getRelation($this->attribute);
        }

        if (! $value) {
            $value = $resource->{$this->attribute}()->withoutGlobalScopes()->getResults();
        } 

        if ($value) { 
            $this->value = $value->mapInto($this->resourceClass)->map(function($schema) {
                return $schema->serializeForDisplay($this->resolvePropertiesForDisplay($schema));
            });
        }
    }
    
    /**
     * Prepare the property for client schema consumption.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function serializeForSchema(Request $request)
    {
        $object = class_basename($this->resourceClass). 'Object';

        return array_merge(parent::serializeForSchema($request), [
            'items' => "array[{$object}]",
            $object => [
                'type'      => 'object',
                'properties'=> $this->resolveProperties($this->resourceClass)
                                    ->map->serializeForSchema($request)
            ],
        ]);
    }
}
