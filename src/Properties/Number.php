<?php

namespace Armincms\Snail\Properties; 

use Armincms\Snail\Contracts\AsNumber; 

class Number extends Property implements AsNumber 
{    
	/**
	 * The decimal points.
	 * 
	 * @var integer
	 */
	public $decimal = 2;

	/**
	 * The decimal separator.
	 *
	 * @var string
	 */
	public $point = '.';

	/**
	 * The thousands separator.
     *
	 * @var string
	 */
	public $thousand = ',';

    /**
     * Get the value of property.
     * 
     * @return mixed
     */
    public function getValue()
    {  
        return $this->isNullValue($this->value) 
                    ? null 
                    : number_format($this->value, $this->decimal, $this->point, $this->thousand);
    } 

    /**
     * Sets the number of decimal points.
     * 
     * @param  int $decimal 
     * @return $this                 
     */
    public function decimal(int $decimal = 0)
    {
    	$this->decimal = $decimal;

    	return $this;
    }

    /**
     * Sets the separator for the decimal point.
     * 
     * @param  string $point 
     * @return $this                 
     */
    public function point(string $point = '.')
    {
    	$this->point = $point;

    	return $this;
    }

    /**
     * Sets the thousands separator.
     * 
     * @param  string|null $separator 
     * @return $this                 
     */
    public function thousand(string $separator = null)
    {
    	$this->thousand = $separator;

    	return $this;
    }
}
