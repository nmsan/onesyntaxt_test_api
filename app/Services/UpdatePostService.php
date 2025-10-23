<?php

namespace App\Services;

use App\Contracts\UpdatePostInterface;
use App\Events\PostPublished;
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

            $oldStatus = $post->status;
            $post->update([
                'title' => $data['title'],
                'body' => $data['body'],
                'status' => $data['status'] ?? $post->status,
            ]);

            $updatedPost = $post->fresh();

            if ($oldStatus !== 'published' && $updatedPost->status === 'published') {
                event(new PostPublished($updatedPost, $updatedPost->website_id));
            }

            return $updatedPost;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
