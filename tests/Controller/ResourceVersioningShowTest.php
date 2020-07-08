<?php

namespace Armincms\Snail\Tests\Controller;

use Armincms\Snail\Tests\IntegrationTest;
use Armincms\Snail\Tests\Fixtures\Post;

class ResourceVersioningShowTest extends IntegrationTest
{

    public function setUp(): void
    {
        parent::setUp(); 
    }

    public function test_can_show_major_resource()
    { 
        $post = factory(Post::class)->create();

        $response = $this->withExceptionHandling()
                        ->getJson("/snail/{$this->major}/resources/posts/{$post->id}");   

        $response->assertStatus(200); 
  
        $this->assertIsFloat($response->original['id']); 
        $this->assertIsString($response->original['name']); 
    }
}