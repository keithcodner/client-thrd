<?php

namespace App\Http\Controllers\Support;

use Illuminate\Http\Request;
use App\Mail\SiteMailServer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class SiteMailController extends Controller
{
    public function sendMail(){
        $details = [
            'title' => 'Welcome to GigBizness',
            'body' => 'Thank you for registering.'
        ];

        Mail::to('contact@test.ca')->send(new SiteMailServer($details));
        return 'Email sent';
    }
}
