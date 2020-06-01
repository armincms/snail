<?php

namespace Armincms\Snail\Properties;

use Closure;
use Armincms\Snail\Snail;
use Armincms\Snail\Schema;

trait FormatsRelatableDisplayValues
{
    /**
     * Format the associatable display value.
     *
     * @param  mixed  $resource
     * @return string
     */
    protected function formatDisplayValue($resource)
    {
        if (! $resource instanceof Schema) {
            $resource = Snail::newResourceFromModel($resource);
        }

        if ($this->display) {
            return call_user_func($this->display, $resource);
        }

        return $resource->title();
    }

    /**
     * Set the column that should be displayed for the field.
     *
     * @param  \Closure|string  $display
     * @return $this
     */
    public function display($display)
    {
        $this->display = $display instanceof Closure
                        ? $display
                        : function ($resource) use ($display) {
                            return $resource->{$display};
                        };

        return $this;
    }
}
