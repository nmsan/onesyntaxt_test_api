<?php

namespace App\Services;

use App\Contracts\Contract\CreatePostInterface;
use App\Models\Post;
use App\Models\Website;

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
            return $post;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
