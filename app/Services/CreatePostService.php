<?php

namespace App\Services;

use App\Contracts\CreatePostInterface;
use App\Events\PostPublished;
use App\Models\Post;

class CreatePostService implements CreatePostInterface
{
    public function createPost($website_id, $data)
    {
        try {
            $post = Post::create(
                [
                    'title' => $data['title'],
                    'body' => $data['body'],
                    'status' => $data['status'] ?? 'draft',
                    'website_id' => $website_id]
            );

            if ($post->status === 'published') {
                event(new PostPublished($post, $website_id));
            }

            return $post;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
