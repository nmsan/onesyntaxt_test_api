<?php

namespace App\Services;

use App\Contracts\WebsiteCreationInterface;
use App\Models\Website;

class WebsiteCreationService implements WebsiteCreationInterface
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
