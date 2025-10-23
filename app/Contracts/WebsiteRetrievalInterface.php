<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface WebsiteRetrievalInterface
{
    public function getAllWebsites(): Collection;
}
