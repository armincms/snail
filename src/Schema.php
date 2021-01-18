<?php  

namespace Armincms\Snail; 

use ArrayAccess;
use JsonSerializable;  
use Armincms\Snail\Http\Requests\SnailRequest; 
use Armincms\Snail\Properties\PropertyCollection; 
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\DelegatesToResource;
use Illuminate\Contracts\Routing\UrlRoutable; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Schema implements ArrayAccess, UrlRoutable
{
    use ConditionallyLoadsAttributes,
        DelegatesToResource, 
        ResolvesProperties,
        ResolvesOrderings,
        ResolvesFilters,
        PerformsQueries;

    /**
     * The underlying model resource instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $resource;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = [];

    /**
     * The per-page options used the resource index.
     *
     * @var array
     */
    public static $perPageOptions = [25, 50, 100];  

    /**
     * Create a new resource instance.
     *
     * @param  mixed $resource
     * @return void
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    } 

    /**
     * Get the properties displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    abstract public function properties(Request $request);

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return Str::plural(Str::title(Str::snake(class_basename(get_called_class()), ' ')));
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return Str::singular(static::label());
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return $this->{static::$title};
    }

    /**
     * Get the URI key for the schema.
     *
     * @return string
     */
    public static function uriKey()
    {
        return Str::plural(Str::kebab(class_basename(get_called_class())));
    }

    /**
     * Get a fresh instance of the model represented by the resource.
     *
     * @return mixed
     */
    public static function newModel()
    {
        $model = static::$model;

        return new $model;
    }

    /**
     * The pagination per-page options configured for this resource.
     *
     * @return array
     */
    public static function perPageOptions()
    {
        return static::$perPageOptions;
    }

    /**
     * Get the searchable columns for the resource.
     *
     * @return array
     */
    public static function searchableColumns()
    {
        return empty(static::$search)
                    ? [static::newModel()->getKeyName()]
                    : static::$search;
    }

    /**
     * Get meta information about this resource for client side comsumption.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function additionalInformation(Request $request)
    {
        return [];
    }  

    /**
     * Prepare the resource for JSON serialization.
     * 
     * @param  \Illuminate\Support\Collection  $properties
     * @return array
     */
    public function serializeForDisplay($properties)
    { 
        return PropertyCollection::make($properties) 
                    ->keyBy('name')
                    ->map->getValue();
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Support\Collection  $properties
     * @return array
     */
    public function serializeForIndex(SnailRequest $request, $properties = null)
    { 
        return $this->serializeForDisplay($properties ?: $this->indexProperties($request));
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return array
     */
    public function serializeForDetail(SnailRequest $request)
    {
        return $this->serializeForDisplay($this->detailProperties($request));
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @return array
     */
    public function serializeForSchema(SnailRequest $request)
    {
        return $this->serializeForDisplay($this->detailProperties($request));
    } 

    /**
     * Get meta data information about all properties for schema.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function propertiesInformation(Request $request)
    {
        $resource = new static(static::newModel());

        return collect($resource->properties($request))->map->serializeForSchema($request);
    }

    /**
     * Get the resource index schema 
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public static function indexSchema(Request $request)
    {
        $object = class_basename(static::class). 'Object';

        return [
            'data' => [
                'type'   => 'array',
                'items'  => "array[{$object}]",
            ],

            $object => [
                'type' => 'object',
                'properties' => collect(static::propertiesInformation($request))
                                    ->where('showOnIndex')
                                    ->values()
                                    ->all(),

            ],

            'meta' => [
                'type' => 'object',
                'properties' => [ 
                    "current_page" => [
                        "type" => "integer"
                    ],
                    "from" => [
                        "type" => "integer"
                    ],
                    "last_page" => [
                        "type" => "integer"
                    ],
                    "path" => [
                        "type" => "string"
                    ],
                    "per_page" => [
                        "type" => "integer"
                    ],
                    "to" => [
                        "type" => "integer"
                    ],
                    "total" => [
                        "type" => "integer"
                    ] 
                ]
            ],

            'links' => [
                'type' => 'object',
                'properties' => [
                    "first" => [
                        "type" => "string",
                        "nullable" => true
                    ],
                    "last" => [
                        "type" => "string",
                        "nullable" => true
                    ],
                    "prev" => [
                        "type" => "string",
                        "nullable" => true
                    ],
                    "next" => [
                        "type" => "string",
                        "nullable" => true
                    ]
                ]
            ],
        ];
    }

    /**
     * Get the resource detail schema.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public static function detailSchema(Request $request)
    {
        return [
            'properties' => collect(static::propertiesInformation($request))
                                ->where('showOnDetail')
                                ->values()
                                ->all(),
        ];
    }  

    /**
     * Get the resource filter schema.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public static function filterSchema(Request $request)
    {
        $resource = new static(static::newModel());

        return collect($resource->filters($request))->map->jsonSerialize()->keyBy('name')->all();
    } 

    /**
     * Get the resource schema.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public static function schema(Request $request)
    {
        return [
            'label' => static::label(), 
            'singularLabel' => static::singularLabel(),
            'show' => [ 
                'request' => [
                    'path'   => '{version}/'.static::uriKey().'/{id}',
                    'method' => 'get',
                    'params'    => [
                        'type' => 'object',
                        'properties' => [
                            'relations' => [
                                'type' => 'array',
                                'items' => 'array[string]'
                            ],
                        ], 
                    ],
                ],
                'response' => array_merge((array) static::detailSchema($request),[
                    'code'   => 200,
                    'type' => 'object',
                ]),
            ],
            'index' => [
                'request' => [
                    'path' => '{version}/'.static::uriKey(),
                    'method' => 'get',
                    'params'    => [
                        'type' => 'object',
                        'properties' => array_merge([  
                            'relations' => [
                                'type' => 'array',
                                'items' => 'array[string]'
                            ],
                            'filters' => (array) static::filterSchema($request)
                        ]) 
                    ],
                ],
                'response' => [
                    'code' => 200,
                    'type' => 'object',
                    'properties' => (array) static::indexSchema($request)
                ],
            ],
        ];
    }
}