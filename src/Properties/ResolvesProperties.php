<?php

namespace Armincms\Snail\Properties; 

 
trait ResolvesProperties 
{       
    /**
     * Resolve the property's value for display.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolveForDisplay($resource, $attribute = null)
    { 
        parent::resolve($resource, $attribute);

        $this->value = $this->resolveVia($resource, __FUNCTION__); 
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

        $this->value = $this->resolveVia($resource, __FUNCTION__); 
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
        return PropertyCollection::make($this->value)
                            ->map(function($value, $attribute) use ($resource) {
                                return $this->prepareUsing($attribute, $value, $resource);
                            }) 
                            ->$resolve($this->value)
                            ->map->getValue()
                            ->all();  
    } 
}
