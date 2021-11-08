<?php

namespace App\Listeners;

use App\Events\OrderStatusChangedEvent;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderStatusChangedListener
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
     * @param  OrderStatusChangedEvent  $event
     * @return void
     */
    public function handle(OrderStatusChangedEvent $event)
    {
        $event->order->customer->notify(new OrderStatusChangedNotification($event->order));
    }
}
