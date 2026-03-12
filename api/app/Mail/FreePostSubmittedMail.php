<?php

namespace App\Mail;

use App\Models\JobPost;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FreePostSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $jobPost;
    public $remainingPosts;

    /**
     * Create a new message instance.
     */
    public function __construct(JobPost $jobPost, int $remainingPosts)
    {
        $this->jobPost = $jobPost;
        $this->remainingPosts = $remainingPosts;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Job Post Has Been Published - GigBizness')
                    ->view('emails.free-post-submitted')
                    ->with([
                        'jobTitle' => $this->jobPost->title,
                        'jobUrl' => url('/jobs/' . $this->jobPost->slug),
                        'companyName' => $this->jobPost->company_name,
                        'remainingPosts' => $this->remainingPosts,
                        'expiresAt' => $this->jobPost->expires_at->format('F j, Y'),
                    ]);
    }
}
