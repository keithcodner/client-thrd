<?php

namespace App\Http\Controllers\Core;

use Carbon\Carbon;
use App\Models\Conversation\ConversationChats;
use App\Models\Conversation\ConversationCategory;
use App\Models\Conversation\ConversationGroupTracker;
use App\Models\Item;
use App\Models\User;
use App\Models\SiteSettings;
use App\Models\Conversation\Conversation;
use Illuminate\Http\Request;
use App\Events\NewChatMessage;
use App\Models\CircleTransactions\CircleTransaction;
use App\Http\Controllers\Controller;
use App\Models\CircleTransactions\CircleTransactionHistory;
use App\Http\Controllers\Core\NotificationsController;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    public function ptChatIndex()
    {
        return Inertia::render('ZProtoTypes/Chat/Chat', [
            'test' => 'test',
        ]);
    }

    //TODO: In conversation table, added chat type for conversation, types can be couple or group 
    //TOOD: added field for group chat id's
    //TODO: added field for group chat deletes
    //TODO: Deleting a chat, also means aborting a circle

    public function index()
    {
        //Get Conversation and Item Data
        $curr_user =  auth()->user()->id;

        $conversations =  Conversation::where(function($q) use ($curr_user){
                                            $q->where('user_id', $curr_user)
                                            ->where(function($q1){
                                                $q1->where('status', 'active')
                                                ->orWhere('status', 'old_chat');
                                            });
                                        })
                                        ->orWhere(function($q) use ($curr_user){
                                            $q->where('from_id', $curr_user)
                                            ->where(function($q1){
                                                $q1->where('status', 'active')
                                                ->orWhere('status', 'old_chat');
                                            });
                                        })
                                        ->orderBy('updated_at', 'DESC')
                                        ->get();

        $conversation_ids = Conversation::where('user_id', $curr_user)
                                            ->orWhere('from_id', $curr_user)
                                            ->pluck('item_id');
      

        $items = Item::whereIn('id', $conversation_ids)->get();

        $first_conv = Conversation::where('user_id', $curr_user)->orWhere('from_id', $curr_user)->first();

        //Need to get something for chats, even if its empty active
        $chats = ConversationChats::where('id', '0')->limit(0)->get();

        if(isset($first_conv)){
            //Grab chats of first conversation or most up to date for this user
            $chats = ConversationChats::where('conversation_id', $first_conv->id)->limit(15)->get();

            //Users should only ever see their own chats
            if(($curr_user === $first_conv->user_id) || ($curr_user === $first_conv->from_id) ){
                return view('old1.pages.chat', [
                    'conversations' => $conversations,
                    'items' => $items,
                    'chats' => $chats,
                ]); 
            }else{
                return view('old1.pages.chat'); 
            }
        }else{
            return view('old1.pages.chat', [
                'conversations' => $conversations,
                'items' => $items,
                'chats' => $chats,
            ]); 
        }
    }

    public function getConversationCount()
    {
        //Get Conversation and Item Data
        $curr_user =  auth()->user()->id;

        $conversations =  Conversation::where(function($q) use ($curr_user){
                                            $q->where('user_id', $curr_user)
                                            ->where(function($q1){
                                                $q1->where('status', 'active')
                                                ->orWhere('status', 'old_chat');
                                            });
                                        })
                                        ->orWhere(function($q) use ($curr_user){
                                            $q->where('from_id', $curr_user)
                                            ->where(function($q1){
                                                $q1->where('status', 'active')
                                                ->orWhere('status', 'old_chat');
                                            });
                                        })
                                        ->orderBy('updated_at', 'DESC')
                                        ->count();
        return ['count' => $conversations];
    }
    
    //We need to use this, because we really should have been using the circleId; but we didn't make the decision to use circles at the time; we decided at the time that users would only use chat. So this works for circle or chat
    public function getSpecificConversationByInitiatorItemId($initiator_item_id){

        $curr_user =  auth()->user()->id;

        $conversations =  Conversation::
                where(function($q) use ($curr_user, $initiator_item_id){
                $q->where('user_id', $curr_user)
                    ->where('item_id', $initiator_item_id)
                    ->where(function($q1){
                        $q1->where('status', 'active')
                        ->orWhere('status', 'old_chat');
                    });
                })
            ->orWhere(function($q) use ($curr_user, $initiator_item_id){
                $q->where('from_id', $curr_user)
                ->where('item_id', $initiator_item_id) 
                ->where(function($q1){
                    $q1->where('status', 'active')
                    ->orWhere('status', 'old_chat');
                });
            })
            ->orderBy('updated_at', 'DESC')
            ->first();

        return $conversations;
    }

    public function getConversations()
    {
        // Get current user ID
        $curr_user = auth()->user()->id;

        // Get conversations
        $conversations = Conversation::where(function($q) use ($curr_user) {
                $q->where('user_id', $curr_user)
                    ->where(function($q1) {
                        $q1->where('status', 'active')
                            ->orWhere('status', 'old_chat');
                    });
            })
            ->orWhere(function($q) use ($curr_user) {
                $q->where('from_id', $curr_user)
                    ->where(function($q1) {
                        $q1->where('status', 'active')
                            ->orWhere('status', 'old_chat');
                    });
            })
            ->orderBy('created_at', 'DESC')
            ->with(['circleTransaction', 'from_id_data', 'user_id_data'])
            ->get();

        $conversation_category = ConversationCategory::where('owner_user_id',  $curr_user)
        ->where('category_status', 'active')
        ->orderBy('created_at', 'DESC')
        ->with(['convoGroupTracker'])
        ->get();
        
        $conversation_group_tracker = ConversationGroupTracker::where('user_id',  $curr_user)
        ->where('tracker_status', 'active')
        ->orderBy('created_at', 'DESC')
        ->get();
        

        $raw_count = $conversations->count();

        // Retrieve chat messages separately and limit them
        $conversations->each(function($conversation) {
            $conversation->chat = $conversation->chat()->orderBy('created_at', 'DESC')->limit(1)->get();
        });

        $conversation_ids = Conversation::where('user_id', $curr_user)
                                        ->orWhere('from_id', $curr_user)
                                        ->pluck('item_id');

        $items = Item::whereIn('id', $conversation_ids)->with(['fileStored'])->get();

        $first_conv = Conversation::where('user_id', $curr_user)
                                ->orWhere('from_id', $curr_user)
                                ->limit(1)
                                ->first();

        // Placeholder for chats
        $chats = ConversationChats::where('id', '0')->limit(0)->get();
        

        if (isset($first_conv)) {
            $chats = ConversationChats::where('conversation_id', $first_conv->id)
                                    ->limit(1)
                                    ->get();

            if (($curr_user === $first_conv->user_id) || ($curr_user === $first_conv->from_id)) {
                return [
                    'conversations' => $conversations,
                    'conversation_category' => $conversation_category,
                    'items' => $items,
                    'chats' => $chats,
                    'raw_count' => $raw_count,
                    'logged_in_user' => $curr_user,
                ];
            } else {
                return 404;
            }
        } else {
            return [
                'conversations' => $conversations,
                'conversation_category' => $conversation_category,
                'items' => $items,
                'chats' => $chats,
                'raw_count' => $raw_count,
                'logged_in_user' => $curr_user,
            ];
        }
    }

    public function createChatInitiatiationByType($type)
    {

    }

    public function chatInit_ProNetwork(Request $request)
    {

    }

    public function chatInit_Circle(Request $request)
    {
        //Get Conversation and Item Data
        $page = 'chat.doschat'; //pages.doschat
        $curr_user =  auth()->user()->id;
        $conversations = Conversation::where(function($q) use ($curr_user){
                                            $q->where('user_id', $curr_user)
                                            ->where(function($q1){
                                                $q1->where('status', 'active')
                                                ->orWhere('status', 'old_chat');
                                            });
                                        })
                                        ->orWhere(function($q) use ($curr_user){
                                            $q->where('from_id', $curr_user)
                                            ->where(function($q1){
                                                $q1->where('status', 'active')
                                                ->orWhere('status', 'old_chat');
                                            });
                                        })
                                        ->orderBy('updated_at', 'DESC')
                                        ->get();
        

        $conversation_ids  =  Conversation::where('user_id', $curr_user)->orWhere('from_id', $curr_user)->pluck('item_id');
        
        $items = Item::whereIn('id', $conversation_ids)->get();
        $item_data = Item::where('id', $request->item_id)->get();
        $first_conv = Conversation::where('user_id', $curr_user)->orWhere('from_id', $curr_user)->first();

        //Generate Chat ID
        $chat_an_id = uniqid().'-'.uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;
        $conv_an_id = uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;

        //Get Data for Conversation
        $chat_id = ConversationChats::where('chat_an_id', $chat_an_id)->pluck('id');
        
        $end_user_fname = User::where('id', $request->user_id)->pluck('firstname'); //get person you messaged id first name
        $init_user_fname  = User::where('id', $request->from_id)->pluck('firstname'); //get person you messaged id last name

        $create_chat = '';
        $create_conversation = '';

        //Determine if the conversation already exists item
        if (
            Conversation::where('item_id', $request->item_id)->where('from_id', auth()->user()->id)->exists() ||
            Conversation::where('item_id', $request->item_id)->where('user_id', auth()->user()->id)->exists() 
        ) {

            return back()->with('status', 'You already have a conversation with this item');
            //GO BACK
            //TODO: If it exists, but one party has deleted the conversation, create a new conversation
            //TODO: If it exists but the user is blocked
        }else{
            //**** START TO CREATE INITIATION ONCE ALL REQUIRED DATA IS GATHERED ****

            $second_type = 'circle'; //see chat enums of front end for different types
            $create_conversation = Conversation::create([
                'user_id' => $request->user_id, // other user
                'from_id' => $request->from_id, // I clicked the msg btn
                'item_id' => $request->item_id,
                'conv_an_id' => $conv_an_id,
                'type_second' =>  $second_type,
                'title' => $init_user_fname[0]. ' to ' .$end_user_fname[0],
            ]);

            $conv_id = Conversation::where('user_id', $request->user_id)->where('from_id', $request->from_id)->where('item_id', $request->item_id)->pluck('id');

            $create_chat = ConversationChats::create([
                'init_user_id' => $request->from_id, // I clicked the msg btn
                'end_user_id' => $request->user_id, //other user
                'conversation_id' => $conv_id[0],
                'chat_an_id' => $chat_an_id,
                'content' => 'Hey there, I am interested in your '."'". $item_data[0]->ip_title ."'".' item!',
            ]);

            $notif_an_id = uniqid().'-'.uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;

            $circle_an_id = uniqid().'-'.uniqid().'-'.now()->timestamp;
            $circle_history_an_id = uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;

            $circle_title_temp =  'To ' .$end_user_fname[0] . ' for ' . $item_data[0]->ip_title;
            $default_time_limit = SiteSettings::where('name', 'circle_time_limit_tier_1')->first();
            $site_settings_who_initiates_circle_offer = SiteSettings::where('name', 'circle_who_initiates_offer')->first();

            $circle_id_prospect_item_value_or_null = null;

            //Determines who picks the counter offer; prospect or initiator
            if($site_settings_who_initiates_circle_offer->value  == 'prospect'){
                $circle_id_prospect_item_value_or_null = null;
            }else if($site_settings_who_initiates_circle_offer->value  == 'initiator'){
                $circle_id_prospect_item_value_or_null = $request->initiator_item_id;
            }
            
            $create_circleTransaction = CircleTransaction::create([
                'circle_id_initiator' => $request->from_id, //i clicked the button
                'circle_id_prospect' => $request->user_id, //other user
                'circle_id_prospect_item' => $circle_id_prospect_item_value_or_null, //the item the prospect (or initiator) presents to the initiator
                'circle_id_initiator_item' => $request->item_id, //the item the initiator wants
                'circle_conversation_id' => $create_conversation->id,
                'circle_transaction_an_id' =>  $circle_an_id,
                'circle_initiation_date' => Carbon::now(),
                'circle_type' => 'item',
                'circle_title' => $circle_title_temp,
                'circle_initiator_title' => $circle_title_temp,
                'circle_prospect_title' => $circle_title_temp,
                'circle_status' => 'active',
                'circle_second_status' => 'prospect_incoming', //Determines whether prospect is interested or not - other status is prospect_accepted
                'circle_time_status_type' => 'normal_time', //regular 30 days, other statuses could be 1 day limit or 5 day limit, etc
                'circle_isInDispute' => 'false',
                'circle_initiatorTrustScore' => '0',
                'circle_initiator_hasAgreed' => 'true',
                'circle_prospect_hasAgreed' => 'false',
                'circle_theme_code' => $this->pickTheme(),
                'circle_completion_date' => Carbon::now()->addDays($default_time_limit->value), //default time is 30 days
            ]);

            $create_circleTransactionHistory = CircleTransactionHistory::create([
                'circle_trans_id' => $create_circleTransaction->id,
                'circle_id_initiator_item' => $request->item_id, //i clicked the button
                'circle_id_prospect_item' => $circle_id_prospect_item_value_or_null, //other user
                'circle_trans_history_an_id' => $circle_history_an_id,
                'status' => 'valid',
                'circle_initiator_hasAgreed' => 'false',
                'circle_prospect_hasAgreed' => 'false',
                'transaction_summary' => 'Circle Transaction Created',
                //'circle_completion_date' => $test, //let this be null
            ]);
            
            //Set notification only if its a new message
            //Send Notification to other participant
            //Example: generateSiteNotification($title, $description, $msg_type, $user_id, $from_id)
            app(NotificationsController::class)->generateSiteNotification(
                auth()->user()->firstname.' has messaged you',
                'This person was interested in your '."'". $item_data[0]->ip_title ."'".' item',
                'chat_message',
                $request->user_id,
                $request->from_id,
                'false',
            );
        }

        //Grab conversation
        $conv_id = Conversation::where('user_id', $request->user_id)->where('from_id', $request->from_id)->where('item_id', $request->item_id)->pluck('id');

        //Need to get something for chats, even if its empty
        $chats =  ConversationChats::where('id', '0')->limit(0)->get();
        return redirect()->route('doschat');
    }

    public function pickTheme(){
        $progressItems = array('style1', 'style2', 'style3', 'style4', 'style5', 'style6', 'style7', 'style8', 'style9');

        $randomItem = array_rand($progressItems, 1);
        $test = shuffle($progressItems);

        //return  $progressItems[$randomItem[0]];
        //dd($progressItems[$randomItem]);
        return $progressItems[$randomItem];
    }

    public function chatConverseClick(Request $request){
        $conv_id = $request->value1;
        $conv_chats = ConversationChats::where('conversation_id', $request->value1)->orderBy('created_at', 'DESC')->limit(15)->get()->toJson();

        //update read chats if you happen to be the end user
        if(ConversationChats::where('conversation_id', $request->value1)->where('seen_by_other_user', "false")->where('end_user_id', auth()->user()->id)->exists()){
             $update_convo1 = ConversationChats::where('conversation_id', $request->value1)->where('end_user_id', auth()->user()->id)->update([
            "seen_by_other_user" => "true",
             ]);
        }
        
        //update read chats if you happen to be the initiator
        if(ConversationChats::where('conversation_id', $request->value1)->where('seen_by_other_user', "false")->where('init_user_id', auth()->user()->id)->exists()){
             $update_convo2 = ConversationChats::where('conversation_id', $request->value1)->where('init_user_id', auth()->user()->id)->update([
                 "seen_by_other_user" => "true",
            ]);
        }

        return $conv_chats;
    }

    //Determins which user to delete
    public function otherDeleteUserId($user_id, $from_id, $logged_in_user_id){

        $end_id = '';
        if ($user_id == $logged_in_user_id) {
            $end_id = $from_id;
        } else {
            $end_id = $user_id; 
        }

        return $end_id;
    }

    public function chatUserDetails(Request $request){
        $other_user_id = $request->value1;
        $firstname = User::where('id', $other_user_id)->pluck('firstname')->first();
        //$lastname = User::where('id', $other_user_id)->pluck('lastname')->first();
        //$name = $firstname . ' ' . $lastname;
        //dd($conv_chats);

        return $firstname;
    }

    public function postChat(Request $request){
        //$chat_an_id = uniqid().'-'.uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;
        $chat_an_id = $request->value4;
        
        $newChatMessage = '';

        //this check should stop users from sending messages to themselves
        if(auth()->user()->id == $request->value2){
            // do nothing
            return 'init and end users are the same, they should not be';
        }else if(auth()->user()->id != $request->value2){
            $newChatMessage = ConversationChats::create([
                'init_user_id' => auth()->user()->id, // I clicked the msg btn
                'end_user_id' => $request->value2, //other user
                'conversation_id' => $request->value3,
                'seen_by_other_user' => 'false',
                'seen_by_received_user' => 'false',
                'chat_an_id' => $chat_an_id,
                'content' => $request->value1,
            ]);
        }

        broadcast(new NewChatMessage($newChatMessage))->toOthers();
        //event(new NewChatMessage($newChatMessage));
        return 'user id: ' . auth()->user()->id . ' -  end user id: '. $request->value2;
    }

    public function pollChat(Request $request){

        if(rand(1,3) == 1){
            /* Fake an error */
            //header("HTTP/1.0 404 Not Found");
            //die();
        }
        
        /* Send a string after a random number of seconds (2-10) */
        //sleep(rand(2,5));
        
        $conv_id = $request->value1;
        $conv_chats = ConversationChats::orderBy('updated_at', 'ASC')->limit(1)->get()->toJson();
        $conv_chats = ConversationChats::where('conversation_id', $conv_id)->where('seen_by_other_user', 'false')->orderBy('updated_at', 'ASC')->limit(30)->get()->toJson();

        //dd($conv_chats);

        return $conv_chats;
    }

    public function checkIfOtherChatIsRead(Request $request){
        $chat_id = $request->value1;
        $conv_chats = ConversationChats::where('id', $chat_id)->update([
            "seen_by_other_user" => "true"
        ]);

    }

    /*
                        ***** destoryConversation *****
        - Destorying a conversation, also means this is aborting a circle
        - At the time of initially deleting a conversation, the circle feature didn't exist
    */
    public function destoryConversation(Request $request){
        $convo_id = $request->value1; //gets conversation id
        $delete_initiator_id = $request->value2; // logged in user id 
        $recipUser = $request->value3; //the delete convo initiator (from recipient)
        $fromUser = $request->value4; //the delete convo initiator (could be me or my convo partner)

        $completed_circle = 'false';
         //where to determine a conversation is deleted or completed...or anything else

        if(isset($request->value5)){
            $completed_circle = $request->value5;
        }else{
            $completed_circle = 'false';
        }

        if(trim($delete_initiator_id) == trim($recipUser)){ //person who init the convo delete, is the convo receiver

            if($completed_circle == 'false'){
                $update_convo = Conversation::where('id', $convo_id)->update([
                    "deleted_by_user_id" => "true",
                    "status" => "old_chat",
                ]);
            }else if($completed_circle == 'true'){
                $update_convo = Conversation::where('id', $convo_id)->update([
                    "status_second" => "completed",
                    "status" => "old_chat",
                ]);
            }
            

            if(Conversation::where('id', $convo_id)->where('deleted_by_from_id', 'true')->where('deleted_by_user_id', 'true')->exists()){
                $update_convo = Conversation::where('id', $convo_id)->update([
                    "status" => "marked_for_deletion",
                ]);
            }

            //Send Notification to other participant
            // Example: generateSiteNotification($title, $description, $msg_type, $user_id, $from_id)
            app(NotificationsController::class)->generateSiteNotification(
                auth()->user()->firstname.'has deleted this message.',
                '*SYSTEM MESSAGE: The other user has ended the conversation, they will not see or respond to future messages. This conversation will be deleted in 30 days.*',
                'chat_message',
                trim($fromUser),
                trim($delete_initiator_id),
                'false',
            );

        }else if(trim($delete_initiator_id) == trim($fromUser)){ //person who init the convo delete, is the convo initiator

            if($completed_circle == 'false'){
                $update_convo = Conversation::where('id', $convo_id)->update([
                    "deleted_by_from_id" => "true",
                    "status" => "old_chat",
                ]);
            }else if($completed_circle == 'true'){
                $update_convo = Conversation::where('id', $convo_id)->update([
                    "status_second" => "completed",
                    "status" => "old_chat",
                ]);
            }
            

            if(Conversation::where('id', $convo_id)->where('deleted_by_from_id', 'true')->where('deleted_by_user_id', 'true')->exists()){
                $update_convo = Conversation::where('id', $convo_id)->update([
                    "status" => "marked_for_deletion",
                ]);
            }

            //Send Notification to other participant
            // Example: generateSiteNotification($title, $description, $msg_type, $user_id, $from_id)
            app(NotificationsController::class)->generateSiteNotification(
                auth()->user()->firstname.' has deleted this message.',
                '*SYSTEM MESSAGE: The other user has ended the conversation, they will not see or responde to future messages. This conversation will be deleted in 30 days.*',
                'chat_message',
                trim($recipUser),
                trim($delete_initiator_id),
                'false',
            );
        }

        //Abort the circle
        CircleTransaction::where('circle_conversation_id', $convo_id)->update([
            'circle_status' => 'aborted'
        ]);

       
        $got_here = '';

        return  trim($delete_initiator_id) . '  - [' . trim($recipUser ). ']  - [' . trim($fromUser). '] - '. $got_here;

    }

    /*
                    ***** Chat Conversation Category Routes *****
    */

    public function addChatCategoryGroup(Request $request){
        ConversationCategory::create([
            'owner_user_id' => auth()->user()->id,
            'category_an_id'  => app(SiteHelperController::class)->createAlphaNumericId(),
            'category_name'  => $request->value1['name'],
            'category_description'  =>'default',
            'category_expand_state'  =>'closed',
            'category_status'  => 'active',
            'category_type'  => 'custom',
        ]);
    }

    public function updateChatCategoryGroup(Request $request){
        ConversationCategory::where('id', $request->value1['id'])->update([
            'category_name'  => $request->value1['name'],
        ]);
    }

    public function deleteChatCategoryGroup(Request $request){
        //TODO: Either we need to remove all conversations to default or require the user to remove all items from the category before we delete the conversation category folder
        //dd($request->value1);

        //check if convo_cat_id exists in conversation_group_tracker table before deleting
        $convo_cat = ConversationCategory::where('id', $request->value1)->first();

        if (
            ConversationGroupTracker::where('convo_cat_id',  $convo_cat->id)->exists() 
        ) {
            return 'This folder still has items in it, cannot delete. Remove all items before attempting to delete';
        }else{
            $convo_cat->delete();
        }
    }

     /*
                                TODO: Scenario's that will be a problem
            - add conversation to the conversation group (requires updating the convo_cat_id in the conversation_group_tracker table)
            - folder buinsess rules:
            --> CANNOT have other users able to see other peoples conversation/conversation folders (one id change can make this mistake, need hard rules for this)
            --> CANNOT have more than 1 duplicate convo_id in the conversation_group_tracker per user_id. convo_cat_id, convo_id, and user_id must all be unique...if we have it where an 3 id columns, are duplicated, there will be problems. (ie.this is a hard rule, and must not be broken ). See visual example below:

            user ids: 3 - Klint, 4 - John, they share convo_id 77

            row | convo_cat_id | convo_id |  user_id
            0           1           2           3        ------> Good
            1           1           2           3        ------> Bad: Duplicates first row           
            2           1           11          4        ------> Bad: Another user is using the first users convo folder
            3           3           22          3        ------> Bad: Klint is usings Johns folder
            4           2           77          3        ------> Good
            5           3           33          4        ------> Good  
            6           3           55          4        ------> Bad: Duplicates first row    
            7           3           55          4        ------> Bad: Duplicates first row    
            8           3           77          4        ------> Good
            9           3           77          4        ------> Bad: Conversation is in more than 2 peopls folder
            --> CANNOT have the SAME conversation in MULTIPLE conversation categories
    */

    public function addConvoToConvoCategory(Request $request){

        //TODO: Should do proper checks before updating as well
        ConversationGroupTracker::where('id',  $request->value1['selectedConversationGroupTrackerId'])
        ->update([
            'convo_cat_id' => $request->value1['selectedFolder']
        ]);
        //dd($request);
    }

    public function removeConvoFromConvoCategory(Request $request){

        //find users default folder
        $defaultFolder = ConversationCategory::where('owner_user_id', auth()->user()->id)->where('category_name', 'Default')->first();

        //assign single conversation back to default 
        ConversationGroupTracker::where('id', $request->value1['selectedConversationGroupTrackerId'])
        ->update([
            'convo_cat_id' => $defaultFolder->id
        ]);
    }

    public function resetConversationsToDefaultConversationCategory(Request $request){
        //find users default folder
        $defaultFolder = ConversationCategory::where('owner_user_id', auth()->user()->id)->where('category_name', 'Default')->first();

        // Get current user ID
        $curr_user = auth()->user()->id;

        // Get conversations
        $conversations = Conversation::where(function($q) use ($curr_user) {
                $q->where('user_id', $curr_user)
                    ->where(function($q1) {
                        $q1->where('status', 'active')
                            ->orWhere('status', 'old_chat');
                    });
            })
            ->orWhere(function($q) use ($curr_user) {
                $q->where('from_id', $curr_user)
                    ->where(function($q1) {
                        $q1->where('status', 'active')
                            ->orWhere('status', 'old_chat');
                    });
            })
            ->orderBy('created_at', 'ASC')
            ->get();

        foreach ($conversations as $conversation) {
            ConversationGroupTracker::where('convo_id', $conversation->id)
            ->update([
                'convo_cat_id' => $defaultFolder->id
            ]);
        }
    }

    public function updateExpandState(Request $request){
        $response = $request->value1['state'];
        //dd($response);
        if($response == true){
            ConversationCategory::where('id', $request->value1['convo_id'])->update([
                'category_expand_state'  => 'opened',
            ]);
            return 'opened';
        }else if($response == false){
            ConversationCategory::where('id', $request->value1['convo_id'])->update([
                'category_expand_state'  => 'closed',
            ]);
            return 'closed';
        }
    }

    
}
