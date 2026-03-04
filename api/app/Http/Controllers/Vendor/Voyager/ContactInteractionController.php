<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\Comment\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactInteractionController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    public function index()
    {
        try {
            $contacts = Comment::where('comm_type', 'contact_message')
                ->where('comm_status', '!=', 'deleted')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return Inertia::render('Admin/Support/ContactInteractions/ContactInteractions', [
                'contacts' => $contacts,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching contact interactions: ' . $e->getMessage());
            return Inertia::render('Admin/Support/ContactInteractions/ContactInteractions', [
                'contacts' => [],
                'error' => 'Failed to load contact interactions.'
            ]);
        }
    }

    public function reply(Request $request)
    {
        try {
            $request->validate([
                'contact_id' => 'required|exists:comments,id',
                'reply_message' => 'required|string|min:3|max:2000',
            ]);

            $contact = Comment::findOrFail($request->contact_id);
            
            // Here you would typically send an email to the contact
            // For now, we'll just log the reply and update the contact status
            Log::info('Reply sent to contact', [
                'contact_id' => $contact->id,
                'contact_email' => $contact->comm_email,
                'reply_message' => $request->reply_message,
                'admin_user' => Auth::user()->id
            ]);

            // Update contact status to indicate it has been replied to
            $contact->update([
                'comm_status' => 'replied'
            ]);

            // TODO: Implement actual email sending logic here
            // Mail::to($contact->comm_email)->send(new ContactReplyMail($request->reply_message));

            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending contact reply: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reply. Please try again.'
            ], 500);
        }
    }

    public function archive(Request $request)
    {
        try {
            $request->validate([
                'contact_id' => 'required|exists:comments,id',
            ]);

            $contact = Comment::findOrFail($request->contact_id);
            $contact->update([
                'comm_status' => 'archived'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contact message archived successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error archiving contact: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive contact message.'
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'contact_id' => 'required|exists:comments,id',
            ]);

            $contact = Comment::findOrFail($request->contact_id);
            $contact->update([
                'comm_status' => 'deleted'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contact message deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting contact: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete contact message.'
            ], 500);
        }
    }

    public function getContact(Request $request)
    {
        try {
            $request->validate([
                'contact_id' => 'required|exists:comments,id',
            ]);

            $contact = Comment::findOrFail($request->contact_id);

            return response()->json([
                'success' => true,
                'contact' => $contact
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching contact: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch contact details.'
            ], 500);
        }
    }
}
