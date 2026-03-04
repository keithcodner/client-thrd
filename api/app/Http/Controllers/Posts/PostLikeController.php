<?php

namespace App\Http\Controllers\Posts;

use App\Models\Posts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostLikeController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        
    }

    public function store(Posts $post, Request $request)
    {
       // dd($post->likedBy($request->user()));
       if($post->likedBy($request->user()->id)){
           return response(null, 409);
       }

       //dd($request->user()->id);
       $post->likes()->create([
           'user_id' => $request->user()->id,
           'lk_type' => 'normal',
           'lk_value' => 'up',
       ]);

       return back();
    }

    public function destroy(Posts $post, Request $request)
    {
        //dd($request->user()->likes()->where('post_id', $post->user_id)->delete());
        $request->user()->likes()->where('post_id', $post->id)->delete();

        return back();
    }
}
