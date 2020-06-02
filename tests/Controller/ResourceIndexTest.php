<?php

namespace Armincms\Snail\Tests\Controller;

use Armincms\Snail\Tests\IntegrationTest;
use Armincms\Snail\Tests\Fixtures\Post;

class ResourceIndexTest extends IntegrationTest
{

    public function setUp(): void
    {
        parent::setUp(); 
    }

    public function test_can_list_a_resource()
    {
        factory(Post::class)->create();
        factory(Post::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->withExceptionHandling()
                        ->getJson("/snail/{$this->version}/resources/posts");    

        $response->assertStatus(200);


        $this->assertEquals('Post Resources', $response->original['label']);
        $this->assertEquals('Post Resource', $response->original['singularLabel']);
 
        $response->assertJsonCount(3, 'data'); 
        $this->assertEquals($post->id, $response->original['data'][0]['ID']);   
    }
}