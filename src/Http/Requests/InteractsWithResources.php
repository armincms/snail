<?php

namespace Armincms\Snail\Http\Requests;

use Armincms\Snail\Snail;

trait InteractsWithResources
{ 
    /**
     * Get the class name of the resource being requested.
     *
     * @return mixed
     */
    public function resource()
    { 
        return tap(Snail::resourceForKey($this->route('resource')), function ($resource) { 
            abort_if(is_null($resource), 404);
            // abort_if(! $resource::authorizedToViewAny($this), 403); 
        });
    }

    /**
     * Get a new instance of the resource being requested.
     *
     * @return \Armincms\Snail\Schema
     */
    public function newResource()
    {
        $resource = $this->resource();

        return new $resource($this->model());
    }

    /**
     * Find the resource model instance for the request.
     *
     * @param  mixed|null  $resourceId
     * @return \Armincms\Snail\Schema
     */
    public function findResourceOrFail($resourceId = null)
    {
        return $this->newResourceWith($this->findModelOrFail($resourceId));
    }

    /**
     * Find the model instance for the request.
     *
     * @param  mixed|null  $resourceId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findModelOrFail($resourceId = null)
    {
        if ($resourceId) {
            return $this->findModelQuery($resourceId)->firstOrFail();
        }

        return once(function () {
            return $this->findModelQuery()->firstOrFail();
        });
    }

    /**
     * Get the query to find the model instance for the request.
     *
     * @param  mixed|null  $resourceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findModelQuery($resourceId = null)
    {
        return $this->newQueryWithoutScopes()->whereKey(
            $resourceId ?? $this->resourceId
        );
    }

    /**
     * Get a new instance of the resource being requested.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Armincms\Snail\Schema
     */
    public function newResourceWith($model)
    {
        $resource = $this->resource();

        return new $resource($model);
    }

    /**
     * Get a new query builder for the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        return $this->model()->newQuery();
    }

    /**
     * Get a new, scopeless query builder for the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQueryWithoutScopes()
    {
        return $this->model()->newQueryWithoutScopes();
    }

    /**
     * Get a new instance of the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        $resource = $this->resource();

        return $resource::newModel();
    }
}
