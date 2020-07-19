<?php

namespace Armincms\Snail\Properties; 

use Armincms\Snail\Contracts\AsObject;

class Collection extends Property implements AsObject
{       
    /**
     * Indicates if the field value cast as array.
     *
     * @var bool
     */
    public $asArray = false;

    /**
     * Indicate that the field value should cast as array.
     *
     * @param  bool  $asArray 
     * @return $this
     */
    public function asArray($asArray = true)
    {
        $this->asArray = $asArray; 

        return $this;
    }

    /**
     * Get the type of property value.
     * 
     * @return string
     */
    protected function getValueType()
    {
    	return $this->asArray ? 'array' : parent::getValueType();
    }
}
