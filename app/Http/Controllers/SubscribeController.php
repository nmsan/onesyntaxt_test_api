<?php

namespace App\Http\Controllers;

use App\Contracts\SubscribeServiceInterface;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    public function store(SubscribeServiceInterface $subscribeService, Request $request)
    {   return $subscribeService->subscribe($request->user_id, $request->website_id);
    }
}
