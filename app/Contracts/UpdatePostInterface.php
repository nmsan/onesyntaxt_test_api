<?php

namespace App\Contracts;

interface UpdatePostInterface
{
    public function updatePost($post_id, $data);
}
