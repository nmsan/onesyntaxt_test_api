<?php

namespace App\Contracts;

interface SubscribeServiceInterface
{
    public function subscribe($user_id, $website_id);
}
