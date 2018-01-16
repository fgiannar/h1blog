<?php

namespace App\Events;

use App\Post;
use Illuminate\Queue\SerializesModels;

class PostCreated extends Event
{
    /**
     * Create a new event instance.
     *
     * @param  Post  $post
     * @return void
     */
    public function __construct(Post $post)
    {
         $this->post = $post;
    }
}
