<?php

namespace App\Http\Controllers;

use App\Contracts\SubscribeServiceInterface;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    public function store(SubscribeServiceInterface $subscribeService, Request $request)
    {
        $result = $subscribeService->subscribe($request->user_id, $request->website_id);
        
        return response()->json([
            'message' => $result['message'],
            'status' => $result['status'],
            'data' => $result['status'] ? new SubscriptionResource($result['data'] ?? null) : null
        ], 200);
    }
}
