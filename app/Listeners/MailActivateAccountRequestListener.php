<?php

namespace App\Listeners;

use App\Events\MailActivateAccountRequestEvent;
use App\Notifications\MailActivateAccountRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MailActivateAccountRequestListener
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
     * @param  MailActivateAccountRequestEvent  $event
     * @return void
     */
    public function handle(MailActivateAccountRequestEvent $event)
    {
        $event->user->notify(new MailActivateAccountRequestNotification());
    }
}
