<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Mail\PostCreatedEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;


class SendNewPostNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PostCreated  $event
     * @return void
     */
    public function handle(PostCreated $event)
    {
        $post = $event->post;
        Mail::to(env('ADMIN_EMAIL'))->send(new PostCreatedEmail($post));
    }
}
