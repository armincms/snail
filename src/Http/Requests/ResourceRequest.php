<?php

namespace Armincms\Snail\Http\Requests;

use Armincms\Snail\Contracts\MustBeAuthenticated;

class ResourceRequest extends SnailRequest
{
    use InteractsWithResources/*, InteractsWithRelatedResources*/; 

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
    	return with($this->resource(), function($resource) {
    		return $resource instanceof MustBeAuthenticated 
    					? app('auth')->guard($resource->authenticateVia())->check()
    					: false
    	}); 
    }
}
