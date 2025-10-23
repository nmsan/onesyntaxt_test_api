<?php

namespace App\Contracts\Contract;

interface SubscribeServiceInterface
{
    public function subscribe($user_id, $website_id);
}
