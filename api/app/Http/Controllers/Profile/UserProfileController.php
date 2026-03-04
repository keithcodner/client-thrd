<?php

namespace App\Http\Controllers\Core;

use App\Models\Item;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SiteSettings;
use App\Http\Controllers\Controller;

class UserProfileController extends Controller
{
    public function __construct()
    {
        ////$this->middleware(['auth']);
    }

    public function index(User $user)
    {
        $items = Item::where('user_id', $user->id)->where('ip_status', 'active')->paginate(30);
        $item_sum = Item::where('user_id', $user->id)->sum('ip_views');
        $sub_data = '';
        $site_settings = SiteSettings::where('name', 'require_content_validation')->first();

        if(Subscription::where('subber', auth()->id())->where('subbee', $user->id)->where('isSubberSubbed', 'true')->exists()){
            $sub_data = 'subbed';
        }else{
            $sub_data = 'not_subbed';
        }

        return view('old1.pages.profile', [
            'user' => $user,
            'items' => $items,
            'item_sum' => $item_sum,
            'sub_data' => $sub_data,
            'require_content_validation' => $site_settings->value
        ]);
    }
}
