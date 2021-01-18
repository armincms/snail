<?php

namespace Armincms\Snail\Orderings;

use JsonSerializable;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Armincms\Snail\Snail;
use Armincms\Snail\Metable;  
use Armincms\Snail\Contracts\Ordering as OrderingContract;

abstract class Ordering implements OrderingContract, JsonSerializable
{
    use Metable;  

    /**
     * The displayable name of the ordering.
     *
     * @var string
     */
    public $name; 

    /**
     * Apply the ordering to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $direction)
    {
        $query->orderingBy($this->column(), $direction);
    }

    /**
     * Get the displayable name of the ordering.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Snail::humanize($this);
    }

    /**
     * Get the column for the ordering.
     *
     * @return string
     */
    public function column()
    {
        return Str::snake(class_basename($this));
    }

    /**
     * Set the default options for the ordering.
     *
     * @return array|mixed
     */
    public function default()
    {
        return 'ASC';
    }

    /**
     * Prepare the ordering for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    { 
        return array_merge([
            'column' => $this->column(), 
            'name' => $this->name(),  
            'direction' => $this->default(),
        ], $this->meta());
    }
}
