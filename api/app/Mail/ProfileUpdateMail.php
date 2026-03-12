<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfileUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $changes;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, array $changes = [])
    {
        $this->user = $user;
        $this->changes = $changes;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Profile Updated - GigBizness')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->view('emails.profile-updated');
    }
}
