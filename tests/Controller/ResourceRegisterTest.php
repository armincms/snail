<?php

namespace Armincms\Snail\Tests\Controller;

use Armincms\Snail\Tests\IntegrationTest;
use Armincms\Snail\Tests\Fixtures\PageResource;
use Armincms\Snail\Tests\Fixtures\PostResource;
use Armincms\Snail\Tests\Fixtures\PostResourceVersioning;
use Armincms\Snail\Snail;

class ResourceRegisterTest extends IntegrationTest
{ 
    public function test_register_resource()
    {    
        $this->assertSame(Snail::getResources(), [
            PostResource::class, 
            PageResource::class 
        ]);
    }

    public function test_register_major_resource()
    {        
        Snail::version($this->major, function($snail) { 
            $this->assertSame($snail->getResources(), [
                PostResourceVersioning::class, 
                PageResource::class 
            ]);
        });  
    }
}