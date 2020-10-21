<?php

namespace Armincms\Snail\Contracts; 

interface MustBeAuthenticated
{    
    /**
     * Determine the authentication guard.
     * 
     * @return string
     */
	public function authenticateVia() : string;
}
