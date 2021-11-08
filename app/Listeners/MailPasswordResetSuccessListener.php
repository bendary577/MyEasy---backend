<?php

namespace App\Listeners;

use App\Events\MailPasswordResetSuccessEvent;
use App\Notifications\MailActivateAccountRequestNotification;
use App\Notifications\MailPasswordResetSuccessNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MailPasswordResetSuccessListener
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
     * @param  MailPasswordResetSuccessEvent  $event
     * @return void
     */
    public function handle(MailPasswordResetSuccessEvent $event)
    {
        $event->user->notify(new MailPasswordResetSuccessNotification());
    }
}
