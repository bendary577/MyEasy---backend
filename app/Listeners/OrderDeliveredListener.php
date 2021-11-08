<?php

namespace App\Listeners;

use App\Events\OrderDeliveredEvent;
use App\Notifications\OrderDeliveredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderDeliveredListener
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
     * @param  OrderDeliveredEvent  $event
     * @return void
     */
    public function handle(OrderDeliveredEvent $event)
    {
        $event->order->customer->notify(new OrderDeliveredNotification($event->order));
    }
}
