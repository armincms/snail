<?php 

namespace Armincms\Snail; 

use Armincms\Snail\Events\ServingSnail;
use Illuminate\Support\Facades\Event; 
use Symfony\Component\Finder\Finder;
use Illuminate\Http\Request;
use BadMethodCallException; 
use ReflectionClass;

class Snail
{	    
    /**
     * The registered resource names.
     *
     * @var array
     */
    public static $resources = [];

    /**
     * An index of resource names keyed by the model name.
     *
     * @var array
     */
    public static $resourcesByModel = []; 
    
    /**
     * Get the current Snail version.
     *
     * @return string
     */
    public static function version()
    {
        return '0.1.0';
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
     * Register the given resources.
     *
     * @param  array  $resources
     * @return static
     */
    public static function resources(array $resources)
    {
        static::$resources = array_unique(
            array_merge(static::$resources, $resources)
        );

        return new static;
    }

    /**
     * Register all of the schema classes in the given directory.
     *
     * @param  string  $directory
     * @return void
     */
    public static function schemasIn($directory)
    {
        $namespace = app()->getNamespace();

        $resources = [];
        $collections = [];

        foreach ((new Finder)->in($directory)->files() as $resource) {
            $resource = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($resource->getPathname(), base_path('App').DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($resource, Resource::class) &&
                ! (new ReflectionClass($resource))->isAbstract() &&
                ! (is_subclass_of($resource, ActionResource::class))) {
                $resources[] = $resource;
            }
        }

        static::resources(
            collect($resources)->sort()->all()
        );
    }

    /**
     * Get the resource class name for a given key.
     *
     * @param  string  $key
     * @return string
     */
    public static function resourceForKey($key)
    {
        return collect(static::$resources)->first(function ($value) use ($key) {
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

        if (isset(static::$resourcesByModel[$class])) {
            return static::$resourcesByModel[$class];
        }

        $resource = collect(static::$resources)->first(function ($value) use ($class) {
            return $value::$model === $class;
        });

        return static::$resourcesByModel[$class] = $resource;
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