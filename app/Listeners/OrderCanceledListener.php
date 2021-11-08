<?php

namespace App\Listeners;

use App\Events\OrderCanceledEvent;
use App\Notifications\OrderCanceledNotification;
use App\Notifications\OrderDeliveredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderCanceledListener
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
     * @param  OrderCanceledEvent  $event
     * @return void
     */
    public function handle(OrderCanceledEvent $event)
    {
        $event->order->customer->notify(new OrderCanceledNotification($event->order));
    }
}
