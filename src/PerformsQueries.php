<?php

namespace Armincms\Snail;

use Armincms\Snail\Http\Requests\SnailRequest;
use Armincms\Snail\Query\ApplySoftDeleteConstraint;

trait PerformsQueries
{
    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @param  array  $filters
     * @param  array  $orderings
     * @param  string  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildIndexQuery(SnailRequest $request, $query, $search = null,
                                      array $filters = [], array $orderings = [],
                                      $withTrashed = TrashedStatus::DEFAULT)
    {
        return static::applyOrderings(static::applyFilters(
            $request, static::initializeQuery($request, $query, $search, $withTrashed), $filters
        ), $orderings)->tap(function ($query) use ($request) {
            return static::indexQuery($request, $query->with(static::$with));
        });
    }

    /**
     * Initialize the given index query.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @param  string  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function initializeQuery(SnailRequest $request, $query, $search, $withTrashed)
    {
        if (empty(trim($search))) {
            return static::applySoftDeleteConstraint($query, $withTrashed);
        }

        return static::usesScout()
                ? static::initializeQueryUsingScout($request, $query, $search, $withTrashed)
                : static::applySearch(static::applySoftDeleteConstraint($query, $withTrashed), $search);
    }

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applySearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $model = $query->getModel();

            $connectionType = $query->getModel()->getConnection()->getDriverName();

            $canSearchPrimaryKey = is_numeric($search) &&
                                   in_array($query->getModel()->getKeyType(), ['int', 'integer']) &&
                                   ($connectionType != 'pgsql' || $search <= PHP_INT_MAX) &&
                                   in_array($query->getModel()->getKeyName(), static::$search);

            if ($canSearchPrimaryKey) {
                $query->orWhere($query->getModel()->getQualifiedKeyName(), $search);
            }

            $likeOperator = $connectionType == 'pgsql' ? 'ilike' : 'like';

            foreach (static::searchableProperties() as $property) {
                $query->orWhere($model->qualifyProperty($property), $likeOperator, '%'.$search.'%');
            }
        });
    }

    /**
     * Initialize the given index query using Laravel Scout.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @param  string  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function initializeQueryUsingScout(SnailRequest $request, $query, $search, $withTrashed)
    {
        $keys = tap(static::applySoftDeleteConstraint(
            static::newModel()->search($search), $withTrashed
        ), function ($scoutBuilder) use ($request) {
            static::scoutQuery($request, $scoutBuilder);
        })->take(static::$globalSearchResults)->get()->map->getKey();

        return static::applySoftDeleteConstraint(
            $query->whereIn(static::newModel()->getQualifiedKeyName(), $keys->all()), $withTrashed
        );
    }

    /**
     * Scope the given query for the soft delete state.
     *
     * @param  mixed  $query
     * @param  string  $withTrashed
     * @return mixed
     */
    protected static function applySoftDeleteConstraint($query, $withTrashed)
    {
        return $query;
    }

    /**
     * Apply any applicable filters to the query.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyFilters(SnailRequest $request, $query, array $filters)
    {
        collect($filters)->each->__invoke($request, $query);

        return $query;
    }

    /**
     * Apply any applicable orderings to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $orderings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyOrderings($query, array $orderings)
    {
        $orderings = array_filter($orderings);

        if (empty($orderings)) {
            return empty($query->getQuery()->orders)
                        ? $query->latest($query->getModel()->getQualifiedKeyName())
                        : $query;
        }

        foreach ($orderings as $property => $direction) {
            $query->orderBy($property, $direction);
        }

        return $query;
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(SnailRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Laravel\Scout\Builder  $query
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(SnailRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(SnailRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(SnailRequest $request, $query)
    {
        return $query;
    }
}
