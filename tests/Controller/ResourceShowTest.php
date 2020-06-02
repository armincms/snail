<?php

namespace Armincms\Snail\Tests\Controller;

use Armincms\Snail\Tests\IntegrationTest;
use Armincms\Snail\Tests\Fixtures\Post;

class ResourceShowTest extends IntegrationTest
{

    public function setUp(): void
    {
        parent::setUp(); 
    }

    public function test_can_show_a_resource()
    { 
        $post = factory(Post::class)->create();

        $response = $this->withExceptionHandling()
                        ->getJson("/snail/{$this->version}/resources/posts/{$post->id}");    

        $response->assertStatus(200); 
  
        $this->assertIsInt($response->original['ID']); 
        $this->assertIsString($response->original['name']); 
    }
}