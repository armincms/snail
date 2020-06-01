<?php

namespace Armincms\Snail\Http\Requests;

trait QueriesResources
{
    use DecodesFilters;

    /**
     * Transform the request into a query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function toQuery()
    { 
        $resource = $this->resource();

        return $resource::buildIndexQuery(
            $this, $this->newQuery(), $this->search,
            $this->filters()->all(), $this->orderings(), $this->trashed()
        );
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
     * Get a new query builder for the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQueryWithoutScopes()
    {
        return $this->model()->newQueryWithoutScopes();
    }

    /**
     * Get the orderings for the request.
     *
     * @return array
     */
    public function orderings()
    {
        return ! empty($this->orderBy)
                        ? [$this->orderBy => $this->orderByDirection]
                        : [];
    }

    /**
     * Get the trashed status of the request.
     *
     * @return string
     */
    protected function trashed()
    {
        return $this->trashed;
    }
}