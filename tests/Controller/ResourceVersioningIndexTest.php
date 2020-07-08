<?php

namespace Armincms\Snail\Tests\Controller;

use Armincms\Snail\Tests\IntegrationTest;
use Armincms\Snail\Tests\Fixtures\Post;

class ResourceVersioningIndexTest extends IntegrationTest
{

    public function setUp(): void
    {
        parent::setUp(); 
    }

    public function test_can_list_major_resource()
    {
        factory(Post::class)->create();
        factory(Post::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->withExceptionHandling()
                        ->getJson("/snail/{$this->major}/resources/posts");    

        $response->assertStatus(200);


        $this->assertEquals('Post Resource Versionings', $response->original['label']);
        $this->assertEquals('Post Resource Versioning', $response->original['singularLabel']);
 
        $response->assertJsonCount(3, 'data'); 
        $this->assertEquals($post->id, $response->original['data'][0]['id']);   
    }
}