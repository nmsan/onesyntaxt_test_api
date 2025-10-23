<?php

namespace App\Contracts;

interface CreatePostInterface
{
    public function createPost($website_id, $data);
}
