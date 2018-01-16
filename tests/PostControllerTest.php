<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PostControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test creating a Post with errors.
     *
     * @return void
     */
    public function testCreatingPostWithErrors()
    {
        $this->post('/api/v1/posts', [])
             ->seeJsonEquals([
                "title" => [
                    "The title field is required."
                ],
                "body" => [
                    "The body field is required."
                ],
                "author_id" => [
                    "The author id field is required."
                ],
                "tags" => [
                    "The tags field is required."
                ]
             ]);

        $this->post('/api/v1/posts', [
            "title" => "Lorem ipsum",
            "body" => "Magni fuga veniam explicabo ex rerum dolorum voluptatibus. Officiis quia labore atque natus at. Et sed maiores consequuntur perferendis maxime mollitia odit.",
            "tags" => ["sport"],
            "published_at" => "2022-03-12 07:14:26",
            "author_id" => 99999999,
            ])
             ->seeJsonEquals([
                "author_id" => [
                    "The selected author id is invalid."
                ],
                "published_at" => [
                    "The published at must be a date before or equal to now."
                ],
             ]);
    }

    public function testCreatePost()
    {
        $this->expectsEvents('App\Events\PostCreated');

        $author = factory('App\User')->create();

        $this->post('/api/v1/posts', [
            "title" => "Lorem ipsum",
            "body" => "Magni fuga veniam explicabo ex rerum dolorum voluptatibus. Officiis quia labore atque natus at. Et sed maiores consequuntur perferendis maxime mollitia odit.",
            "tags" => ["sport<script>"],
            "published_at" => "2017-03-12 07:14:26",
            "author_id" => $author->id,
            ])
             ->seeJson([
                "title" => "Lorem ipsum",
                "body" => "Magni fuga veniam explicabo ex rerum dolorum voluptatibus. Officiis quia labore atque natus at. Et sed maiores consequuntur perferendis maxime mollitia odit.",
                "tags" => ["sport"], // sanitized
                "published_at" => "2017-03-12 07:14:26",
                "author_id" => $author->id,
             ]);
    }

    public function testGetPosts()
    {
        $author = factory('App\User')->create();
        $post1 = factory('App\Post')->create(['author_id' => $author->id, 'published_at' => null, 'tags' => ['sport', 'social']]);

        $post2 = factory('App\Post')->create(['author_id' => $author->id, 'published_at' => '2017-03-12 07:14:26', 'tags' => ['sport']]);

        $post3 = factory('App\Post')->create(['author_id' => $author->id, 'published_at' => '2018-01-12 07:14:26', 'tags' => ['weather']]);

        $callUrl = '/api/v1/posts?author_id=' . $author->id;
        $posts = $this->get($callUrl)->response->original;
        $this->assertEquals(sizeof($posts), 3);

        $callUrl .= '&published=1';
        $posts = $this->get($callUrl)->response->original;
        $this->assertEquals(sizeof($posts), 2);

        $callUrl .= '&order=desc';
        $posts = $this->get($callUrl)->response->original;
        $this->assertEquals($posts[0]['published_at'], '2018-01-12 07:14:26');

        $callUrl .= '&tag=weather';
        $posts = $this->get($callUrl)->response->original;
        $this->assertEquals(sizeof($posts), 1);
        $this->assertEquals($posts[0]['tags'], ['weather']);
    }
}
