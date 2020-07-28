<?php 

namespace Armincms\Snail; 

use BadMethodCallException; 
use ReflectionClass;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use Illuminate\Support\Collection; 
use Illuminate\Support\Facades\Event; 
use Symfony\Component\Finder\Finder;
use Armincms\Snail\Events\ServingSnail;

class Snail
{	     
    /**
     * Get the current Snail version.
     *
     * @var array
     */
    public const VERSION = '0.1.0';

    /**
     * The default `API` version.
     *
     * @var array
     */
    public const DEFAULT = '1.0.0';

    /**
     * The current API version.
     *
     * @var integer
     */
    protected static $version;

    /**
     * The registered resource names.
     *
     * @var array
     */
    protected static $resources = [];

    /**
     * An index of resource names keyed by the model name.
     *
     * @var array
     */
    protected static $resourcesByModel = []; 

    /**
     * Get the current API version.
     * 
     * @return string
     */
    public static function currentVersion() : string
    {
        return static::$version ?? static::DEFAULT;
    }

    /**
     * Set the default api version.
     * 
     * @return static
     */
    public static function setDefaultVersion()
    {
        static::$version = static::DEFAULT;

        return new static;
    }

    /**
     * Set the current API version.
     * 
     * @param string $version
     * @return static
     */
    public static function setVersion(string $version)
    {
        static::$version = $version;

        return new static;
    }

    /**
     * Prepare the snail for the given version.
     * 
     * @param  string   $version  
     * @param  callable $callback 
     * @return static             
     */
    public static function version(string $version, callable $callback)
    { 
        $current = static::currentVersion();

        $value = $callback(static::setVersion($version));

        static::setVersion($current);

        return $value ?? new static;
    }  

    /**
     * Get the URI path prefix utilized by Snail.
     *
     * @return string
     */
    public static function path()
    {
        return config('snail.path', '/snail');
    }

    /**
     * Register an event listener for the Snail "serving" event.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function serving($callback)
    {
        Event::listen(ServingSnail::class, $callback);
    } 

    /**
     * Humanize the given value into a proper name.
     *
     * @param  string  $value
     * @return string
     */
    public static function humanize($value)
    {
        if (is_object($value)) {
            return static::humanize(class_basename(get_class($value)));
        }

        return Str::title(Str::snake($value, ' '));
    } 

    /**
     * Register the given resources.
     *
     * @param  array  $resources
     * @return static
     */
    public static function resources(array $resources)
    {   
        static::$resources[static::currentVersion()] = $resources;

        return new static;
    }  

    /**
     * Get the registered resources of the current version.
     *  
     * @return array  
     */
    public static function getResources()
    { 
        return  Collection::make(static::$resources)->filter(function($resources, $version) {
                    return version_compare(static::currentVersion(), $version) !== -1;
                })
                ->sortKeysDesc()
                ->flatten()
                ->unique(function($resource) {
                    return $resource::uriKey();
                }) 
                ->values()
                ->all();
    }  

    /**
     * Get the schemas available for the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function availableSchemas(Request $request)
    {
        return collect(static::$resources)->map(function($schemas, $version) use ($request) {
            return static::version($version, function($snail) use ($request) {
                return $snail->resourceInformation($request);
            });
        })->all();
    }

    /**
     * Get the versions available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function availableVersions(Request $request)
    {
        return collect(static::$resources)->keys()->sort()->all();
    }

    /**
     * Get meta data information about all resources for schema.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function resourceInformation(Request $request)
    {
        return collect(static::getResources())->map(function ($resource) use ($request) {
            return array_merge([
                'uriKey'                => $resource::uriKey(),
                'label'                 => $resource::label(),
                'singularLabel'         => $resource::singularLabel(),
                // 'authorizedToCreate' => $resource::authorizedToCreate($request),
                // 'searchable'         => $resource::searchable(), 
            ], $resource::additionalInformation($request));
        })->values()->all();
    }

    /**
     * Get the resource class name for a given key.
     *
     * @param  string  $key 
     * @return string
     */
    public static function resourceForKey($key)
    {
        return Collection::make(static::getResources())->first(function ($value) use ($key) {
            return $value::uriKey() === $key;
        });
    }

    /**
     * Get a new resource instance with the given model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Armincms\Snail\Resource
     */
    public static function newResourceFromModel($model)
    {
        $resource = static::resourceForModel($model);

        return new $resource($model);
    }

    /**
     * Get the resource class name for a given model class.
     *
     * @param  object|string  $class
     * @return string
     */
    public static function resourceForModel($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (isset(static::$resourcesByModel[static::currentVersion()][$class])) {
            return static::$resourcesByModel[static::currentVersion()][$class];
        }

        $resource = Collection::make(static::getResources())->first(function ($value) use ($class) {
            return $value::$model === $class;
        });

        return static::$resourcesByModel[static::currentVersion()][$class] = $resource;
    }

    /**
     * Get a resource instance for a given key.
     *
     * @param  string  $key
     * @return \Armincms\Snail\Resource|null
     */
    public static function resourceInstanceForKey($key)
    {
        if ($resource = static::resourceForKey($key)) {
            return new $resource($resource::newModel());
        }
    }

    /**
     * Get a fresh model instance for the resource with the given key.
     *
     * @param  string  $key
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function modelInstanceForKey($key)
    {
        $resource = static::resourceForKey($key);

        return $resource ? $resource::newModel() : null;
    } 

    /**
     * Dynamically proxy static method calls.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return void
     */
    public static function __callStatic($method, $parameters)
    {
        if (! property_exists(get_called_class(), $method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        return static::${$method};
    }
}