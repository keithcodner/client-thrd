<?php

namespace App\Http\Controllers\Core;

use App\Models\Comment\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        return Inertia::render('Front/ContactUs');
    }

    public function store(Request $request)
    {
        $comm_an_id = uniqid().'-'.uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:255',
            'message' => 'required',
            'message_type' => 'required|in:general,dmca,complaint,feedback,bug',
        ]);

        $contact = Comment::create([
            'comm_name' => $request->name,
            'comm_comment' => $request->message,
            'comm_email' => $request->email,
            'comm_type' => 'contact_message',
            'comm_s_status' => $request->message_type,
            'comm_an_id' =>  $comm_an_id,
        ]);

        return Redirect::back()->with('message', 'Your message has been sent successfully!');
    }
}
