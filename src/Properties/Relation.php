<?php

namespace Armincms\Snail\Properties;
 
use Illuminate\Support\Str; 
use Illuminate\Http\Request; 
use Armincms\Snail\Http\Requests\SnailRequest;
use Armincms\Snail\Http\Requests\ResourceIndexRequest;  
use Armincms\Snail\Contracts\ListableProperty;
use Armincms\Snail\Contracts\AsObject;

abstract class Relation extends Property implements AsObject
{
    use FormatsRelatableDisplayValues/*, ResolvesReverseRelation*/;

    /**
     * The class name of the related resource.
     *
     * @var string
     */
    public $resourceClass;

    /**
     * The URI key of the related resource.
     *
     * @var string
     */
    public $resourceName;

    /**
     * The name of the Eloquent "belongs to" relationship.
     *
     * @var string
     */
    public $relationship; 

    /**
     * The column that should be displayed for the field.
     *
     * @var \Closure
     */
    public $display;    

    /**
     * The callback to be used to serialize the property's value.
     *
     * @var \Closure
     */
    public $propertiesCallback;  

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  string|null  $resource
     * @return void
     */
    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute);

        $resource = $resource ?? ResourceRelationshipGuesser::guessResource($name);

        $this->resourceClass = $resource;
        $this->resourceName = $resource::uriKey();
        $this->relationship = $this->attribute; 
        $this->propertiesCallback = function($schema) {
            return [ 
                ID::make(Str::camel(Str::singular($this->name).'Id'), function() use ($schema) { 
                    return $schema->getKey();
                }),

                Text::make('title', function() use ($schema) {
                    return $this->formatDisplayValue($schema);
                })
            ];
        };
    }

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
            $schema = new $this->resourceClass($value);

            $this->value = $schema->serializeForDisplay($this->resolvePropertiesForDisplay($schema));
        }
    }

    /**
     * Define the callback that should be used to resolve the field's value.
     *
     * @param  callable  $displayCallback
     * @return $this
     */
    public function displayUsing(callable $displayCallback)
    {
        return $this->display($displayCallback);
    }

    /**
     * Format the associatable display value.
     *
     * @param  mixed  $schema
     * @return string
     */
    protected function resolvePropertiesForDisplay($schema)
    {   
        return $this->resolveProperties($schema)->resolveForDisplay($schema);           
    }

    /**
     * Format the associatable display value.
     *
     * @param  mixed  $schema
     * @return string
     */
    protected function resolveProperties($schema)
    {   
        return PropertyCollection::make(call_user_func($this->propertiesCallback, $schema));        
    }

    /**
     * Specify the callback to be executed to retrieve the related properties.
     *
     * @param  \Closure|string  $display
     * @return $this
     */
    public function properties(Closure $properties)
    {  
        $this->propertiesCallback = $properties;

        return $this;
    }

    /**
     * Determine if the field should be displayed for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorize(Request $request)
    {
        return true;
        return $this->isNotRedundant($request) && call_user_func(
            [$this->resourceClass, 'authorizedToViewAny'], $request
        ) && parent::authorize($request);
    }

    /**
     * Determine if the field is not redundant.
     *
     * Ex: Is this a "user" belongs to field in a blog post list being shown on the "user" detail page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function isNotRedundant(Request $request)
    {
        return ! $request instanceof ResourceIndexRequest || ! $this->isReverseRelation($request);
    } 

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge([ 
            'relationship' => $this->relationship, 
            'resourceName' => $this->resourceName,
        ], parent::jsonSerialize());
    }
}
