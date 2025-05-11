<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
   
    public function index()
    {
        return Post::with(['comments', 'tags'])->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_title' => 'required|string|max:255',
            'post_content' => 'required|max:255',
            'tags' => 'array',
            'tags.*' => 'integer|exists:tags,id',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $post = Post::create([
            'post_title' => $request->post_title,
            'post_content' => $request->post_content,
            'user_id' => auth()->id(),
        ]);


        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }
        return response()->json($post, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return $post->load(['comments', 'tags']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Post $post)
    {
        $post->update($request->all());

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(null, 204);
    }
}

