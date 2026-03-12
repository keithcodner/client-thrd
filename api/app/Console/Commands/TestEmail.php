<?php

namespace App\Console\Commands;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test welcome email to verify email configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Attempting to send test email to: ' . $email);
        
        try {
            // Create a test user object
            $testUser = new User([
                'firstname' => 'Test',
                'lastname' => 'User',
                'email' => $email
            ]);
            
            // Send the email
            Mail::to($email)->send(new WelcomeMail($testUser));
            
            $this->info('✓ Email sent successfully!');
            $this->info('Check your inbox at: ' . $email);
            $this->info('Also check your spam/junk folder.');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('✗ Failed to send email!');
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}
