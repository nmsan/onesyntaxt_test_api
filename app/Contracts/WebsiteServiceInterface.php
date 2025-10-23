<?php

namespace App\Contracts;

use App\Models\Website;

interface WebsiteServiceInterface
{
    public function createWebsite(array $data): Website;
}
