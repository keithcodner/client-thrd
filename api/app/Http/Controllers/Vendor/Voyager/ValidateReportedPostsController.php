<?php

namespace App\Http\Controllers\Vendor\Voyager;

use Carbon\Carbon;
use App\Models\Like;
use App\Models\User;
use App\Models\Posts;
use App\Models\Comment;
use App\Models\Incident;
use App\Models\Core\FileStored;
use Illuminate\Http\Request;
use App\Models\GigBiznessSettings;
use App\Models\Core\FileTemporary;
use App\Models\Core\FilePostStored;
use App\Models\IncidentCatalog;
use App\Models\ContentManagement\ContentReporting;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use League\CommonMark\Util\ArrayCollection;
use App\Http\Controllers\Core\NewsCircleFeedController;
use App\Http\Controllers\Core\NotificationsController;
use Inertia\Inertia;

class ValidateReportedPostsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        Paginator::useBootstrap();
    }

    public function index(Request $request)
    {
        $incident_check = Incident::first();
        $pagination_delimit = 20;
        $active_status = '';
        if(isset($request->active)){
            $active_status = $request->active;
        }

        if($request->active == 'true'){
            $incidents = Incident::where('incident_target_reason_table', 'post')
            ->where(function($q){
                $q->where('incident_status', 'active')
                    ->orWhere('incident_status', 'unread');
            })->paginate($pagination_delimit);

            return Inertia::render('Admin/ContentManagement/ValidateReportedPosts', [
                'incidents' => $incidents->items(),
                'active_status' => $active_status,
                'pagination' => [
                    'current_page' => $incidents->currentPage(),
                    'last_page' => $incidents->lastPage(),
                    'per_page' => $incidents->perPage(),
                    'total' => $incidents->total()
                ]
            ]);
        }else if($request->active == 'false'){
            $incidents = Incident::where('incident_target_reason_table', 'post')
            ->where(function($q){
                $q->where('incident_status', 'closed')
                    ->orWhere('incident_status', 'unread');
            })->paginate($pagination_delimit);

            return Inertia::render('Admin/ContentManagement/ValidateReportedPosts', [
                'incidents' => $incidents->items(),
                'active_status' => $active_status,
                'pagination' => [
                    'current_page' => $incidents->currentPage(),
                    'last_page' => $incidents->lastPage(),
                    'per_page' => $incidents->perPage(),
                    'total' => $incidents->total()
                ]
            ]);
        }else{
            $incidents = Incident::where('incident_target_reason_table', 'post')
            ->where(function($q){
                $q->where('incident_status', 'active')
                    ->orWhere('incident_status', 'unread');
            })->paginate($pagination_delimit);

            return Inertia::render('Admin/ContentManagement/ValidateReportedPosts', [
                'incidents' => $incidents->items(),
                'active_status' => $active_status,
                'pagination' => [
                    'current_page' => $incidents->currentPage(),
                    'last_page' => $incidents->lastPage(),
                    'per_page' => $incidents->perPage(),
                    'total' => $incidents->total()
                ]
            ]);
        }
    }

    public function incidentStatusUpdate(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
            'incident_id' => 'required'
        ]);

        //TODO:Add section to add comment from the system of what happened with status changes
        if($request->status == 'active' && Auth::user()->role_id == 1){
            Incident::where('id', $request->incident_id)->update([
                'incident_status' => $request->status
            ]);
        }else if($request->status == 'closed' && Auth::user()->role_id == 1){
            Incident::where('id', $request->incident_id)->update([
                'incident_status' => $request->status
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Incident status updated successfully']);
    }

    public function postStatusUpdate(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
            'incident_id' => 'required'
        ]);

        $data1 = ContentReporting::where('incident_id', $request->incident_id)->where('reason_table', 'post')->first();
        
        if (!$data1) {
            return response()->json(['success' => false, 'message' => 'Incident data not found'], 404);
        }
        
        //TODO:Add section to add comment from the system of what happened with status changes
        if($request->status == 'inactive' && Auth::user()->role_id == 1){
            Posts::where('id', $data1->reason_id)->update([
                'status' => $request->status
            ]);
            
            //Grab post data
            $post = Posts::where('id', $data1->reason_id)->first();

            //Send  notification to post owner
            app(NotificationsController::class)->generateSiteNotification(
                Auth::user()->firstname.' '.Auth::user()->lastname.' has messaged you about your post.',
                'Your post with the description <i>"'.$post->body.'"</i> has been made inactive by our administrative team. We deemed that it goes against our guideline and termns of service. If you believe this has been done by mistake, please use the contact us form to indicate why you think the status of this post should be changed. <br /><br /> You have 30 days to message us about this, after this time your post will be perminantely deleted. <br /><br /> Thank you, <br /> GigBizness Team',
                'admin_post_message',
                $post->user_id,
                Auth::user()->id,
                'true',
            );
        }else if($request->status == 'active' && Auth::user()->role_id == 1){
            Posts::where('id', $data1->reason_id)->update([
                'status' => $request->status
            ]);

            //Grab post data
            $post = Posts::where('id', $data1->reason_id)->first();

            //Send post to post owner
            app(NotificationsController::class)->generateSiteNotification(
                Auth::user()->firstname.' '.Auth::user()->lastname.' has messaged you about your post.',
                'Your post with the description <i>"'.$post->body.'"</i> has been made active by our administrative team. This item was brought to our attention by user reports, we deemed that this was done wrongfully and have fully re-instated your post. <br /><br /> Thank you, <br /> GigBizness Team',
                'admin_post_message',
                $post->user_id,
                Auth::user()->id,
                'true',
            );
        }else if($request->status == 'inreview' && Auth::user()->role_id == 1){
            Posts::where('id', $data1->reason_id)->update([
                'status' => $request->status
            ]);

            //Grab post data
            $post = Posts::where('id', $data1->reason_id)->first();

            //Send post to post owner
            app(NotificationsController::class)->generateSiteNotification(
                Auth::user()->firstname.' '.Auth::user()->lastname.' has messaged you about your post.',
                'Your post with the description <i>"'.$post->body.'"</i> has been put into "In Review" status by our administrative team. Likely you have messaged us about a post that might have been mistakenly removed. <br /><br /> Our team is looking into this situation and will respond with updates soon. <br /><br /> Thank you, <br /> GigBizness Team',
                'admin_post_message',
                $post->user_id,
                Auth::user()->id,
                'true',
            );
        }

        return response()->json(['success' => true, 'message' => 'Post status updated successfully']);
    }

    public function retrieveIncidentPost(Request $request)
    {
        $this->validate($request, [
            'incident_id' => 'required'
        ]);

        $content_reports = ContentReporting::where('incident_id', $request->incident_id)->where('reason_table', 'post')->first();
        
        if (!$content_reports) {
            return response()->json(['error' => 'Incident not found'], 404);
        }

        $post = Posts::where('id', $content_reports->reason_id)->first();
        
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        // Get post images
        $postImages = FilePostStored::where('post_id', $post->id)->get();
        $images = [];
        foreach ($postImages as $imageFile) {
            $images[] = asset('storage/store_data/posts/' . $imageFile->foldername . '/' . $imageFile->filename);
        }

        return response()->json([
            'id' => $post->id,
            'body' => $post->body,
            'status' => $post->status,
            'created_at' => $post->created_at,
            'images' => $images,
            'user_id' => $post->user_id
        ]);
    }

    public function retreiveIncidentComment(Request $request)
    {
        $this->validate($request, [
            'incident_id' => 'required'
        ]);

        $comments = Comment::where('incident_id', $request->incident_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }
    
    public function updateIncidentComment(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required|min:3',
            'comment_id' => 'required'
        ]);

        $updated = Comment::where('id', $request->comment_id)->update([
            'comm_comment' => $request->comment
        ]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Comment updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to update comment'], 500);
    }

    public function deleteIncidentComment(Request $request)
    {
        $this->validate($request, [
            'comment_id' => 'required'
        ]);

        $deleted = Comment::where('id', $request->comment_id)->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete comment'], 500);
    }

    public function addIncidentComment(Request $request)
    {
        $comm_an_id = uniqid().'-'.uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;
        $comm_an_id_unique = uniqid().'-'.uniqid().'-'.now()->timestamp.'-'.uniqid().'-'.uniqid();
        
        $this->validate($request, [
            'comment' => 'required|min:3',
            'incident_id' => 'required'
        ]);

        $comment = Comment::create([
            'incident_id' => $request->incident_id,
            'comm_name' => Auth::user()->name,
            'user_id' => Auth::user()->id,
            'comm_comment' => $request->comment,
            'comm_type' => 'incident_message',
            'comm_an_id' =>  $comm_an_id,
            'comm_comment_unique_an_id' =>  $comm_an_id_unique,
        ]);

        return response()->json($comment);
    }
}
