<?php

namespace App\Listeners;

use App\Events\UserAccountActivatedEvent;
use App\Notifications\MailActivateAccountRequestNotification;
use App\Notifications\UserAccountActivatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserAccountActivatedListener
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
     * @param  UserAccountActivatedEvent  $event
     * @return void
     */
    public function handle(UserAccountActivatedEvent $event)
    {
        //
        $event->user->notify(new UserAccountActivatedNotification());
    }
}
