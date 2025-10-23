<?php

namespace App\Http\Controllers;

use App\Contracts\CreatePostInterface;
use App\Contracts\UpdatePostInterface;
use App\Models\Post;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    private CreatePostInterface $createPost;
    private UpdatePostInterface $updatePost;

    public function __construct(
        CreatePostInterface $createPost,
        UpdatePostInterface $updatePost
    )
    {
        $this->createPost = $createPost;
        $this->updatePost = $updatePost;
    }

    public function store(int $website_id, Request $request)
    {
        $website = Website::find($website_id);
        if (!$website) {
            return response()->json(['message' => 'Website not found'], 404);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'sometimes|string|in:draft,published'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $post = $this->createPost->createPost($website_id, $request->all());
        if ($post) {
            return response()->json(['message' => 'Post created successfully', 'data' => $post], 200);
        } else {
            return response()->json(['message' => 'Post not created'], 400);
        }
    }

    public function update(int $post_id, Request $request)
    {
        $post = Post::find($post_id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'sometimes|string|in:draft,published'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updatedPost = $this->updatePost->updatePost($post_id, $request->all());
        if ($updatedPost) {
            return response()->json(['message' => 'Post updated successfully', 'data' => $updatedPost], 200);
        } else {
            return response()->json(['message' => 'Post not updated'], 400);
        }
    }
}
