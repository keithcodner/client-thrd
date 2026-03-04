<?php

namespace App\Http\Controllers\Search;

use Throwable;
use App\Models\Conversation\ConversationChats;
use App\Models\Item;
use App\Models\User;
use App\Models\ItemType;
use App\Models\Conversation\Conversation;
use Illuminate\Http\Request;
use App\Models\SiteSettings;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\NotificationsController;

use App\Http\Controllers\Core\PostsController;
use App\Models\ProNetwork\ProNetworkUserProfile;
use App\Models\ProNetwork\ProNetworkRequests;
use App\Models\Posts;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AgoraBrowseFunctionsController extends Controller
{
    public function __construct()
    {
        ////$this->middleware(['auth']);
    }

    //TODO: In conversation table, added chat type for conversation, types can be couple or group
    //TOOD: added field for group chat id's
    //TODO: added field for group chat deletes

    public function index(Request $request)
    {

        return view('old1.search.agora-browse');
    }

    public function categorySearch(string $trade_item_type_id, string $amount){

        $category_name = '';
        $items = Item::where('trade_item_type_id', $trade_item_type_id)->limit($amount)->get();

        //$category_name = ItemType::where('id', $trade_item_type_id)->pluck('trade_item_category_name');

        return $items;
    }

    public function determineAgoraBrowseByTrendingCategories(string $trend, string $amount)
    {
        $items = "";
        if($trend == 'PopularItems'){ //ip_views; most view order by descending
            $items = Item::orderBy('ip_views', 'desc')->where('ip_type', 'item')->where('ip_status', 'active')->limit($amount)->get();
        }else if($trend == 'PopularServices'){ //ip_views; most view order by descending
            $items = Item::orderBy('ip_views', 'desc')->where('ip_type', 'service')->where('ip_status', 'active')->limit($amount)->get();
        }else if($trend == 'LatestItemsAdded'){ //created_at; added lastest order by descending
            $items = Item::orderBy('created_at', 'desc')->where('ip_type', 'item')->where('ip_status', 'active')->limit($amount)->get();
        }else if($trend == 'LatestServicessAdded'){//created_at; added lastest order by descending
            $items = Item::orderBy('created_at', 'desc')->where('ip_type', 'service')->where('ip_status', 'active')->limit($amount)->get();
        }

        return $items ;
    }

    public function generateAgoraCategoryRows(string $categoryTitle, Item $item )
    {

        try{
            $stored_image_path = '';
            $folderz = '';
            $filenamez = '';

            if($item->fileStored[0]->foldername != null){
                $folderz = $item->fileStored[0]->foldername;
            }

            if($item->fileStored[0]->filename != null){
                $filenamez = $item->fileStored[0]->filename;
            }

            //dd($folder);

            $stored_image_path = asset('storage/store_data/items/'. $folderz .'/'. $filenamez);
            //$itemType = $item->itemType();
            //dd($item);

            $truncateTitle = \Illuminate\Support\Str::limit($item->ip_title ,  $limit = 30, $end = '...');
            $fullCategory = $item->itemType->trade_item_subtype;
            $truncateCategory = \Illuminate\Support\Str::limit($item->itemType->trade_item_subtype ,  $limit = 20, $end = '...');
            $categoryLinkSearch = '/search/category/'.$item->itemType->trade_item_subtype;

            $userProfileLink = route('user.profile', $item->user->username);
            $userProfileName = $item->user->username;

            $require_content_validation = '';
            $inner = '';
            $site_settings = SiteSettings::where('name', 'require_content_validation')->first();

            //dd($site_settings->value);
            //dd($item->fileStored[0]->filename);

            //Loop through images connected to item
            foreach($item->fileStored() as $index => $data){
                
                if($site_settings->value == 'yes'){
                    if($data->verify_status == 'verified'){
                        if($data->order == 'first'){
                            $stored_image_path = asset('storage/store_data/items/'. $data->foldername.'/'. $data->filename);
                        }
                    }else{
                        $stored_image_path = asset('storage/users/default-no-image.png');
                    }

                }else if($site_settings->value == 'no'){

                // dd($data->order);

                    if($data->order == 'first'){
                        $stored_image_path = asset('storage/store_data/items/'. $data->foldername.'/'. $data->filename);
                    }else{
                        $stored_image_path = asset('storage/store_data/items/'. $data->foldername.'/'. $data->filename);
                    }
                }
            }

            //Function Definition:
            $data = categoryAgoraInnerComponent(
                route('itemview', $item->id), 
                asset('storage/users/default-no-image.png'), 
                $stored_image_path,
                $truncateTitle,
                $fullCategory,
                $categoryLinkSearch,
                $truncateCategory,
                $userProfileLink,
                $userProfileName,
            );

            return $data;

        }catch(Throwable $ex){

        }
        
    }

    public function generateAgoraCategoryPlacements(Request $request)
    {
        //Config Description
        // 0 = type string (CATEGORY or METRIC), 
        // 1 = category id or string metric, 
        // 2 = category title, 
        // 3 = amount per category row (ORM limit)
        $config = [
            ['METRIC', 'PopularItems', 'Popular Items', '8'],
            ['METRIC', 'PopularServices', 'Popular Services', '8'],
            ['METRIC', 'LatestItemsAdded', 'Latest Items Added', '8'],
            ['METRIC', 'LatestServicessAdded', 'Latest Services Added', '8'],
            ['CATEGORY', '1', 'Audio', '8'],
            ['CATEGORY', '26', 'Toys', '8'],
            ['CATEGORY', '105', 'Furniture', '8'],
        ];

        $output = '';
        $test = '';

        //dd($config[0][0]);

        foreach($config as $items=>$item){
            //dd($config[$items][1]);

            $row = '';
            $isThereData = '';
            if($config[$items][0] == 'METRIC'){
                $ip_items = $this->determineAgoraBrowseByTrendingCategories($config[$items][1], $config[$items][3]); //returns item collection

                $data = '';
                foreach($ip_items as $ip_item){
                    $data .= $this->generateAgoraCategoryRows($config[$items][2], $ip_item); // returns string
                }

                $outputz = categoryAgoraOuterComponent($config[$items][2], $data);
                $isThereData = $data;
                $row = $outputz;

            }else if($config[$items][0] == 'CATEGORY'){
                $ip_items = $this->categorySearch($config[$items][1], $config[$items][3]); //returns item collection

                $data = '';
                foreach($ip_items as $ip_item){
                    $data .= $this->generateAgoraCategoryRows($config[$items][2], $ip_item); // returns string
                }

                $outputz = categoryAgoraOuterComponent($config[$items][2], $data);
                $isThereData = $data;
                $row = $outputz;
            }

            //if nothing is found, dont display the category
            if($isThereData == ""){
                //do nothing
            }else{
                $output .= $row;
            }
            
        }

        return $output;
    }

    //TODO:
    public function determineAgoraBrowseByMetric(Request $request)
    {

    }

    

}
