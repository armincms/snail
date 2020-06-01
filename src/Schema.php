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
        PerformsQueries,
        ResolvesProperties,
        ResolvesFilters;

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
                    ->mapInto(PropertyCollection::class)
                    ->pluck('value', 'name');
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
}