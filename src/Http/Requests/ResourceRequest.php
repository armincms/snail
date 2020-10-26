<?php

namespace Armincms\Snail\Http\Requests;

use Armincms\Snail\Contracts\MustBeAuthenticated;

class ResourceRequest extends SnailRequest
{
    use InteractsWithResources, InteractsWithRelatedResources;  
}
