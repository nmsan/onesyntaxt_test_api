<?php

namespace App\Services;

use App\Contracts\Contract\SubscribeServiceInterface;
use App\Models\Subscription;

class SubscribeService implements SubscribeServiceInterface
{
    public function subscribe($user_id, $website_id)
    {
        try {
            $exist = Subscription::where(['user_id' => $user_id, 'website_id' => $website_id])->exists();
            if ($exist) {
                return ['status' => false, 'message' => 'Already subscribed'];
            }
            $res = Subscription::create(['user_id' => $user_id, 'website_id' => $website_id]);
            if ($res) {
                return [
                    'status' => true,
                    'message' => 'Subscription created successfully'
                    ];
            } else {
                return ['status' => false, 'message' => 'Subscription not created'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
