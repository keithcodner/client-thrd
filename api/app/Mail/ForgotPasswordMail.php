<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;
    public $expiresIn;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $resetUrl, string $expiresIn = '60 minutes')
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
        $this->expiresIn = $expiresIn;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Reset Your Password - GigBizness')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->view('emails.forgot-password');
    }
}
