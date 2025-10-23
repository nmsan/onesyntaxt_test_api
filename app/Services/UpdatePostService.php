<?php

namespace App\Services;

use App\Contracts\Contract\UpdatePostInterface;
use App\Models\Post;

class UpdatePostService implements UpdatePostInterface
{
    public function updatePost($post_id, $data)
    {
        try {
            $post = Post::find($post_id);
            if (!$post) {
                return null;
            }

            $post->update([
                'title' => $data['title'],
                'body' => $data['body'],
                'status' => $data['status'] ?? $post->status,
            ]);

            return $post->fresh();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
