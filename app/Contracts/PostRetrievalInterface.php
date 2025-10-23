<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PostRetrievalInterface
{
    public function getPostsByWebsite(int $websiteId): Collection;
}
