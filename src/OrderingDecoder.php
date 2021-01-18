<?php

namespace Armincms\Snail;

use Illuminate\Support\Str;
use Armincms\Snail\Query\ApplyOrdering;

class OrderingDecoder
{
    /**
     * The orderings array.
     *
     * @var string
     */
    protected $orderings;

    /**
     * The orderings available via the request.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $availableOrderings;

    /**
     * Create a new OrderingDecoder instance.
     *
     * @param  string  $orderings
     * @param  array|null  $availableOrderings
     */
    public function __construct($orderings, $availableOrderings = null)
    {
        $this->orderings = $orderings;
        $this->availableOrderings = collect($availableOrderings);
    }

    /**
     * Decode the given orderings.
     *
     * @return array
     */
    public function orderings()
    { 
        return collect($this->orderings)->map(function ($value, $column) {
            $matchingOrdering = $this->availableOrderings->first(function ($availableOrdering) use ($column) {
                return $column === $availableOrdering->column();
            });  

            if ($matchingOrdering) {
                return ['ordering' => $matchingOrdering, 'value' => Str::upper($value) == 'ASC' ? 'asc' : 'desc'];
            }
        })
            ->filter()
            ->map(function ($ordering) {
                return new ApplyOrdering($ordering['ordering'], $ordering['value']);
            })->values();
    } 
}
