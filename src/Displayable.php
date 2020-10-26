<?php

namespace Armincms\Snail;

use Armincms\Snail\Contracts\Displayable as DisplayableContract;
use Armincms\Snail\Http\Requests\SnailRequest;
use Illuminate\Http\Request;
use JsonSerializable;

abstract class Displayable implements JsonSerializable, DisplayableContract
{
    use Metable; 
    use Makeable;   

    /**
     * Indicates if the property should be shown on the index response.
     *
     * @var \Closure|bool
     */
    public $showOnIndex = true;

    /**
     * Indicates if the property should be shown on the detail response.
     *
     * @var \Closure|bool
     */
    public $showOnDetail = true;   

    /**
     * Indicates if the property should be shown on the realtion response.
     *
     * @var \Closure|bool
     */
    public $showOnRelation = true;   

    /**
     * Determine if the property can display.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  mixed  $schema
     * @return bool
     */
    public function canDisplay(SnailRequest $request, $schema)
    {
        if($request->isIndexRequest()) {
            return $this->isShownOnIndex($request, $schema);
        }

        if($request->isDetailRequest()) {
            return $this->isShownOnDetail($request, $schema);
        }

        return false;
    }

    /**
     * Specify that the property should be hidden from the index response.
     *
     * @param  \Closure|bool  $callback
     * @return $this
     */
    public function hideFromIndex($callback = true)
    {
        $this->showOnIndex = is_callable($callback) ? function () use ($callback) {
            return ! call_user_func_array($callback, func_get_args());
        }
        : ! $callback;

        return $this;
    }

    /**
     * Specify that the property should be hidden from the detail response.
     *
     * @param  \Closure|bool  $callback
     * @return $this
     */
    public function hideFromDetail($callback = true)
    {
        $this->showOnDetail = is_callable($callback) ? function () use ($callback) {
            return ! call_user_func_array($callback, func_get_args());
        }
        : ! $callback;

        return $this;
    } 

    /**
     * Specify that the property should be hidden from the index response.
     *
     * @param  \Closure|bool  $callback
     * @return $this
     */
    public function showOnIndex($callback = true)
    {
        $this->showOnIndex = $callback;

        return $this;
    }

    /**
     * Specify that the property should be hidden from the detail response.
     *
     * @param  \Closure|bool  $callback
     * @return $this
     */
    public function showOnDetail($callback = true)
    {
        $this->showOnDetail = $callback;

        return $this;
    } 

    /**
     * Specify that the property should be hidden from the resource.
     *
     * @param  \Closure|bool  $callback
     * @return $this
     */
    public function showOnRelation($callback = true)
    {
        $this->showOnRelation = $callback;

        return $this;
    } 

    /**
     * Check showing on index.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  mixed  $schema
     * @return bool
     */
    public function isShownOnIndex(SnailRequest $request, $schema): bool
    {
        if (is_callable($this->showOnIndex)) {
            $this->showOnIndex = call_user_func($this->showOnIndex, $request, $schema);
        }

        return $this->showOnIndex;
    }

    /**
     * Check showing on detail.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  mixed  $schema
     * @return bool
     */
    public function isShownOnDetail(SnailRequest $request, $schema): bool
    {
        if (is_callable($this->showOnDetail)) {
            $this->showOnDetail = call_user_func($this->showOnDetail, $request, $schema);
        }

        return $this->showOnDetail;
    } 

    /**
     * Check showing on relation.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  mixed  $schema
     * @return bool
     */
    public function isShownOnRelation(SnailRequest $request, $schema): bool
    {
        if (is_callable($this->showOnRelation)) {
            $this->showOnRelation = call_user_func($this->showOnRelation, $request, $schema);
        }

        return $this->showOnRelation;
    } 

    /**
     * Specify that the property should only be shown on the index response.
     *
     * @return $this
     */
    public function onlyOnIndex()
    { 
        $this->showOnDetail = false; 

        return $this;
    }

    /**
     * Specify that the property should only be shown on the detail response.
     *
     * @return $this
     */
    public function onlyOnDetail()
    { 
        $this->showOnIndex = false; 

        return $this;
    }   
    
    /**
     * Prepare the property for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge($this->meta(), [
            'showOnDetail' => $this->showOnDetail,
            'showOnIndex' => $this->showOnIndex,
        ]);
    }
}
