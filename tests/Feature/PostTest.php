<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_blog_post()
    {
        $response = $this->get('posts');

        $response->assertSeeText('');
    }

    public function test_see_1_blog_post_when_there_is_1()
    {
        // Arrange
        $this->createDummyBlogPost();

        // Act
        $response = $this->get('/posts');

        // Assert
        $response->assertSeeText('New title...');
    }

    public function test_see_1_blog_post_with_comments()
    {
        $post = $this->createDummyBlogPost();

        \App\Models\Comment::factory(5)->create(['blog_post_id' => $post->id]);

        $response = $this->get('/posts');
        $response->assertSeeText('5 comments');
    }

    public function test_store_valid()
    {
        $params = [
            'title' => 'Valid title',
            'content' => 'At least 10 characters'
        ];

        $this->post('/posts', $params)
            ->assertStatus(302)
            ->assertSessionHas('status');
        
        $this->assertEquals(session('status'), 'The blog post was created');
    }

    public function test_store_fail()
    {
        $params = [
            'title' => 'x',
            'content' => 'x'
        ];

        $this->post('/posts', $params)
            ->assertStatus(302)
            ->assertSessionHas('errors');

        // $messages = session('errors');
        // dd($messages->getMessages());

        $messages = session('errors')->getMessages();

        $this->assertEquals($messages['title'][0], 'The title must be at least 10 characters.');
        $this->assertEquals($messages['content'][0], 'The content must be at least 10 characters.');
    }

    private function createDummyBlogPost()
    {
        /*
        $post = new BlogPost();
        $post->title = 'New title...';
        $post->content = 'Content of the blog post';
        $post->save();

        return $post;
        */

        return \App\Models\BlogPost::factory()->create();
    }
}
