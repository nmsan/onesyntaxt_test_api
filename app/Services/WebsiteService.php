<?php

namespace App\Services;

use App\Contracts\WebsiteServiceInterface;
use App\Models\Website;

class WebsiteService implements WebsiteServiceInterface
{
    public function createWebsite(array $data): Website
    {
        return Website::create([
            'name' => $data['name'],
            'url' => $data['url'],
            'user_id' => $data['user_id']
        ]);
    }
}
