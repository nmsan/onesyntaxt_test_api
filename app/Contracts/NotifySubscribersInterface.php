<?php

namespace App\Contracts;

interface NotifySubscribersInterface
{
    public function send($website_id, $data);
}
