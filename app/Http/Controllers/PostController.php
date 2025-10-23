<?php

namespace App\Http\Controllers;

use App\Contracts\Contract\CreatePostInterface;
use App\Models\Website;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private CreatePostInterface $createPost;
    public function __construct(
        CreatePostInterface $createPost
    )
    {
        $this->createPost = $createPost;
    }


    public function store(int $website_id, Request $request)
    {
        $post = $this->createPost->createPost($website_id, $request->all());
        if ($post) {
            return response()->json(['message' => 'Post created successfully', 'data' => $post], 200);
        } else {
            return response()->json(['message' => 'Post not created'], 400);
        }
    }
}
