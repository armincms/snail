<?php

namespace Armincms\Snail\Properties;
  
use Illuminate\Support\Str; 
use Armincms\Snail\Http\Requests\SnailRequest;
use Armincms\Snail\Http\Requests\ResourceIndexRequest; 

class BelongsTo extends Relation 
{  
    /**
     * The displayable singular label of the relation.
     *
     * @var string
     */
    public $singularLabel; 

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  string|null  $resource
     * @return void
     */
    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute, $resource); 

        $this->singularLabel = $name;
    } 

    /**
     * Set the displayable singular label of the resource.
     *
     * @return $this
     */
    public function singularLabel($singularLabel)
    {
        $this->singularLabel = $singularLabel;

        return $this;
    } 

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge([  
            'singularLabel' => $this->singularLabel,
        ], parent::jsonSerialize());
    }
}
