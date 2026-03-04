<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\CommonMark\Util\ArrayCollection;
use Illuminate\Pagination\Paginator;

class ViewSubscriptionRankingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        Paginator::useBootstrap();
    }

    public function index()
    {
        $data = '';

        return view('admin.vendor.voyager.GigBiznessSettings', [
            'data' =>  $data,
        ]);

    }
}
