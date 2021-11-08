<?php

namespace App\Listeners;

use App\Events\NewOrderEvent;
use App\Notifications\NewOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewOrderListener
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
     * @param  NewOrderEvent  $event
     * @return void
     */
    public function handle(NewOrderEvent $event)
    {
        //
        $event->order->seller->notify(new NewOrderNotification($event->order));
    }
}
