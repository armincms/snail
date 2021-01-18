<?php

namespace Armincms\Snail;

use Armincms\Snail\Query\ApplyFilter;

class FilterDecoder
{
    /**
     * The filters array.
     *
     * @var string
     */
    protected $filters;

    /**
     * The filters available via the request.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $availableFilters;

    /**
     * Create a new FilterDecoder instance.
     *
     * @param  string  $filters
     * @param  array|null  $availableFilters
     */
    public function __construct($filters, $availableFilters = null)
    {
        $this->filters = $filters;
        $this->availableFilters = collect($availableFilters);
    }

    /**
     * Decode the given filters.
     *
     * @return array
     */
    public function filters()
    { 
        return collect($this->filters)->map(function ($value, $key) {
            $matchingFilter = $this->availableFilters->first(function ($availableFilter) use ($key) {
                return $key === $availableFilter->key();
            });  

            if ($matchingFilter) {
                return ['filter' => $matchingFilter, 'value' => $value];
            }
        })
            ->filter()
            ->reject(function ($filter) {
                if (is_array($filter['value'])) {
                    return count($filter['value']) < 1;
                } elseif (is_string($filter['value'])) {
                    return trim($filter['value']) === '';
                }

                return is_null($filter['value']);
            })->map(function ($filter) {
                return new ApplyFilter($filter['filter'], $filter['value']);
            })->values();
    } 
}
