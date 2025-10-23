<?php

namespace App\Http\Controllers;

use App\Contracts\CreatePostInterface;
use App\Contracts\UpdatePostInterface;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Website;

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

    public function store(int $website_id, CreatePostRequest $request)
    {
        $website = Website::find($website_id);
        if (!$website) {
            return response()->json(['message' => 'Website not found'], 404);
        }

        $post = $this->createPost->createPost($website_id, $request->validated());
        if ($post) {
            return response()->json(['message' => 'Post created successfully', 'data' => new PostResource($post)], 200);
        } else {
            return response()->json(['message' => 'Post not created'], 400);
        }
    }

    public function update(int $post_id, UpdatePostRequest $request)
    {
        $post = Post::find($post_id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $updatedPost = $this->updatePost->updatePost($post_id, $request->validated());
        if ($updatedPost) {
            return response()->json(['message' => 'Post updated successfully', 'data' => new PostResource($updatedPost)], 200);
        } else {
            return response()->json(['message' => 'Post not updated'], 400);
        }
    }
}
