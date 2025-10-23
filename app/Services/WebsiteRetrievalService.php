<?php

namespace App\Services;

use App\Contracts\WebsiteRetrievalInterface;
use App\Models\Website;
use Illuminate\Database\Eloquent\Collection;

class WebsiteRetrievalService implements WebsiteRetrievalInterface
{
    public function getAllWebsites(): Collection
    {
        return Website::with('user')->get();
    }
}
