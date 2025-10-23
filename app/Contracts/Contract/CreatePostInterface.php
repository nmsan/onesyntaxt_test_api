<?php

namespace App\Contracts\Contract;

interface CreatePostInterface
{
    public function createPost($website_id, $data);
}
