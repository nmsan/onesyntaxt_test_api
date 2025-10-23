<?php

namespace App\Contracts;

use App\Models\Website;

interface WebsiteCreationInterface
{
    public function createWebsite(array $data): Website;
}
