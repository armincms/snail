<?php

namespace Armincms\Snail\Http\Requests;

use Armincms\Snail\Snail;
use Armincms\Snail\Resource;

trait InteractsWithRelatedResources
{ 
    /**
     * Get a new instance of the "via" resource being requested.
     *
     * @return \Armincms\Snail\Resource
     */
    public function newViaResource()
    {
        $resource = $this->viaResource();

        return new $resource($resource::newModel());
    } 

    /**
     * Get the class name of the "via" resource being requested.
     *
     * @return string
     */
    public function viaResource()
    {
        return Snail::resourceForKey($this->viaResource);
    }

    /**
     * Determine if the request is via a relationship.
     *
     * @return bool
     */
    public function viaRelationship()
    {
        return $this->viaResource && $this->viaResourceId && $this->viaRelationship;
    }
}
