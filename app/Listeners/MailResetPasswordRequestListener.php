<?php

namespace App\Listeners;

use App\Events\MailResetPasswordRequestEvent;
use App\Notifications\MailActivateAccountRequestNotification;
use App\Notifications\MailResetPasswordRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MailResetPasswordRequestListener
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
     * @param  MailResetPasswordRequestEvent  $event
     * @return void
     */
    public function handle(MailResetPasswordRequestEvent $event)
    {
        //
        $event->user->notify(new MailResetPasswordRequestNotification($event->passwordReset->token));
    }
}
