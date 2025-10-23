<?php

namespace App\Services;

use App\Contracts\PostRetrievalInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRetrievalService implements PostRetrievalInterface
{
    public function getPostsByWebsite(int $websiteId): Collection
    {
        return Post::where('website_id', $websiteId)
            ->with('website.user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
