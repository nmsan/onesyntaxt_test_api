<?php

namespace App\Contracts\Contract;

interface UpdatePostInterface
{
    public function updatePost($post_id, $data);
}
