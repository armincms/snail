<?php

namespace Armincms\Snail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SnailRequest extends FormRequest
{  
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    } 
    
    /**
     * Determine if this request is a schema index request.
     *
     * @return bool
     */
    public function isIndexRequest()
    {
        return $this instanceof ResourceIndexRequest ||
               $this instanceof CollectionIndexRequest;
    }

    /**
     * Determine if this request is a schema detail request.
     *
     * @return bool
     */
    public function isDetailRequest()
    {
        return $this instanceof ResourceDetailRequest ||
               $this instanceof CollectionDetailRequest;
    }
}
