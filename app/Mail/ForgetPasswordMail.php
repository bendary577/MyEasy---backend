<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($data, $userMail)
    {
        $this->data = $data;
        $this->userMail = $userMail;
    }

    public function build()
    {
        $address = 'mbendary577@gmail.com';
        $subject = 'Reset Password Email';
        $name = 'Mohamed Bendary';

        return $this->view('emails.forgetPasswordMail')
                    ->from($address, $name)
                    ->subject($subject)
                    ->with(['content' => $this->data['content'], 'email' => $this->userMail ]);
    }
}
