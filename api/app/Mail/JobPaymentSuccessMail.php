<?php

namespace App\Mail;

use App\Models\Posts\JobPost;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobPaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $jobPost;
    public $amount;
    public $transactionId;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, JobPost $jobPost, float $amount, string $transactionId = null)
    {
        $this->user = $user;
        $this->jobPost = $jobPost;
        $this->amount = $amount;
        $this->transactionId = $transactionId;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Payment Successful - Your Job is Live!')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->view('emails.job-payment-success');
    }
}
