<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;  
use App\Models\Post;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PostController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'auth:sanctum', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        

        $data = $request->validate([
            'title' => 'required|max:200',
            'body' => 'required',
            
            ]);
       
            // $post = Post::create($data);
        $post = $request->user()->posts()->create($data);
        
        return response()->json(['message' => 'Post created successfully']);

    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
       return  $post ;
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Post $post)
{

              /* Gate::authorize('modify',$post);*/

    if ($request->user()->id !== $post->user_id) {
        return response()->json(['message' => 'you are Unauthorized'], 403);
    }

    $data = $request->validate([
        'title' => 'required|max:200',
        'body' => 'required'
    ]);

    $post->update($data);

    return response()->json($post);
}

public function destroy(Request $request, Post $post)
{
      /* Gate::authorize('modify',$post);*/

    if ($request->user()->id !== $post->user_id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $post->delete();

    return response()->json(['message' => 'Post deleted successfully']);
}

}