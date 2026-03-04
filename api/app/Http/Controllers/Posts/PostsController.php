<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Posts;

class PostsController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }
    
    public function index()
    {
        $posts = Posts::with(['user', 'likes'])->paginate(5);
        return view('old1.pages.posts', [
            'posts' => $posts
        ]);  
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required'
        ]);

        auth()->user()->posts()->create([
            'body' => $request->body,
        ]);
        return back();
    }

    public function destroy(Posts $post)
    {
      $this->authorize('delete', $post); //check postpolicy for method names
      $post->delete();

      //TODO: Need to delete the folders and files in the assets folder

      return back();
    }
}
