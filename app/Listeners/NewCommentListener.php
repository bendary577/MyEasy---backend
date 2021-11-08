<?php

namespace App\Listeners;

use App\Events\NewCommentEvent;
use App\Notifications\NewCommentNotification;
use App\Notifications\NewOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewCommentListener
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
     * @param  NewCommentEvent  $event
     * @return void
     */
    public function handle(NewCommentEvent $event)
    {
        $event->product->store->owner->notify(new NewCommentNotification($event->user, $event->product));
    }
}
