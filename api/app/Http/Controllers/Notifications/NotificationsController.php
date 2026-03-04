<?php

namespace App\Http\Controllers\Core;

use App\Models\Conversation\ConversationChats;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\SiteHelperController;

class NotificationsController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        //notifID
        if(Notification::where('user_id', auth()->user()->id)->exists()){

            //TODO: Add functionality where, if you scroll down past limit, and theres more records, then load them
            if(isset($request->notif)){

                $update_status = Notification::where('notif_an_id', $request->notif)->update([
                    "status" => "read",
                ]);

                $notifications = $this->displaySiteNotifications(99, 'DESC', $request->notif);
                $data = Notification::where('user_id', auth()->user()->id)->where('notif_an_id', $request->notif)->limit(1)->first();

                return view('old1.pages.notifications', [
                    'results' => $notifications,
                    'data' => $data
                ]);
            }else{  
                $notifications = $this->displaySiteNotifications(99, 'DESC');
                return view('old1.pages.notifications', [
                    'results' => $notifications,
                    'data' => ''
                ]);
            }
            
        }else{
            return view('old1.pages.notifications', [
                'results' => 'No Notifications...'
            ]);
        }
    }

    public function store()
    {
        
    }

    public function displaySiteNotifications($limit, $order, $an_id='default')
    {
        $notifications = Notification::where('user_id', auth()->user()->id)->limit($limit)->orderBy('created_at', $order)->get();
            $full_notifications = '';
            foreach($notifications as $notif ){

                $initial = app(SiteHelperController::class)->grabFirstCharacter(trim($notif->title));
                $color = app(SiteHelperController::class)->randomColor();

                $isRead = 'true';
                $isSelected = 'false';

                if($notif->status == 'unread'){
                    $isRead = 'false';
                }

                //determine which notif should be selected
                if(trim($an_id) == 'default'){
                    $isSelected = 'false';
                }else if(trim($an_id) == trim($notif->notif_an_id)){
                    $isSelected = 'true';
                }


                $full_notifications .= notifMessageList(
                    strtoupper($initial),
                    \Illuminate\Support\Str::limit($notif->comment,  $limit = 15, $end = '...'),
                    \Illuminate\Support\Str::limit($notif->title,  $limit = 25, $end = '...'),
                    $notif->id,
                    $isSelected,
                    $notif->color_status.'-'. rand(5,9).'00',
                    $notif->created_at->diffForHumans(),
                    $notif->notif_an_id,
                    strtoupper($notif->status),
                );
            }

        return $full_notifications;
    }

    public function generateSiteNotification($title, $description, $msg_type, $user_id, $from_id, $email='false')
    {
        //Notif id
        $notif_an_id = uniqid().'-'.uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;

        //Set notification only if its a new message
        $notif = Notification::create([
            'user_id' => $user_id, // other user
            'notif_an_id' => $notif_an_id,
            'from_id' => $from_id, // I clicked the msg btn
            'type' => $msg_type,
            'title' => $title,
            'comment' => $description,
            'status' => 'unread',
            'color_status' => app(SiteHelperController::class)->randomColor(),
        ]);

        //determines if we want an email sent as well
        if($email == 'true'){
            $user = User::where('id', $user_id)->first();
            //dd($user->email);
             app(SiteHelperController::class)->sendSiteEmail(
                 $user->email,
                 $title,
                 $description,
                 $user->firstname
             );
        }

        return $notif;
    }

    public function getChatNotifications(Request $request)
    {
        $chat_id = $request->value1;
        $chats = ConversationChats::where('end_user_id', $chat_id)->where('seen_by_other_user', 'false')->count();
        return $chats;
    }

    public function getSiteNotifications(Request $request)
    {
        $notif_user_id = $request->value1;
        $notifs = Notification::where('user_id', $notif_user_id)->where('status', 'unread')->count();
        return $notifs;
    }

    public function getSiteNotificationsData(Request $request)
    {
        $notif_user_id = $request->value1;
        $notifs = Notification::where('user_id', $notif_user_id)->where('status', 'unread')->orderBy('created_at', 'DESC')->limit(25)->get()->toJson();;
        return $notifs;
    }

    public function getIndividualConversationNotificationStatusForAUser(Request $request)
    {
        $chat_end_user_id = $request->value1;
        $chats1 = DB::select("SELECT end_user_id, conversation_id, COUNT(conversation_id) AS 'convo_count' FROM chat WHERE end_user_id = ".$chat_end_user_id." AND seen_by_other_user = 'false' GROUP BY conversation_id;");

        $chats2 = DB::select("SELECT init_user_id, conversation_id, COUNT(conversation_id) AS 'convo_count' FROM chat WHERE init_user_id = ".$chat_end_user_id." AND seen_by_other_user = 'false' GROUP BY conversation_id;");
        
        return [
            "end_user_id_count" => $chats1,
            "init_user_id_count" => $chats2,
        ];
    }

    public function updateSiteNotifStatusOnceRead(Request $request)
    {
        $notif_user_id = $request->id;
        $update_convo = Notification::where('id', $notif_user_id)->update([
            "status" => "read",
        ]);

        //return redirect()->route('notifications');
    }
}
