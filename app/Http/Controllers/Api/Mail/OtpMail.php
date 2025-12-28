<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Your OTP Code')
            ->view('emails.otp')
            ->with([
                'otp' => '123456',
            ]);
    }
}
