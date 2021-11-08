<?php

namespace App\Listeners;

use App\Events\MailCompanyRegisteredVerificationEvent;
use App\Notifications\MailActivateAccountRequestNotification;
use App\Notifications\MailCompanyRegisteredVerificationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MailCompanyRegisteredVerificationListener
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
     * @param  MailCompanyRegisteredVerificationEvent  $event
     * @return void
     */
    public function handle(MailCompanyRegisteredVerificationEvent $event)
    {
        $event->user->notify(new MailCompanyRegisteredVerificationNotification());
    }
}
