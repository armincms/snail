<?php

namespace Armincms\Snail\Http\Requests;

use Armincms\Snail\Contracts\MustBeAuthenticated;

class ResourceRequest extends SnailRequest
{
    use InteractsWithResources, InteractsWithRelatedResources; 

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
    	return with($this->newResource(), function($resource) {  
    		return $resource instanceof MustBeAuthenticated 
    					? with(app('auth'), function($auth) use ($resource) {
                            $auth->shouldUse($resource->authenticateVia());

                            $this->setUserResolver($auth->userResolver());

                            return $auth->check();
                        })
    					: true;
    	}); 
    }
}
