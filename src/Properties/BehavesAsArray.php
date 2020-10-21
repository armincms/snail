<?php

namespace Armincms\Snail\Properties; 
 

trait BehavesAsArray 
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
    public function getValueType()
    {
    	return $this->asArray ? 'array' : parent::getValueType();
    }
}
