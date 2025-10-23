<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_subscribe_to_website()
    {
        $website = Website::factory()->create();
        $user = User::factory()->create();
        $response = $this->postJson('/api/subscribe', [
            'website_id' => $website->id,
            'user_id' => $user->id
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Subscription created successfully'
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'website_id' => $website->id,
            'user_id' => $user->id
        ]);
    }

    public function test_user_cannot_subscribe_to_website_twice()
    {

        $website = Website::factory()->create();
        $user = User::factory()->create();

        $this->postJson('/api/subscribe', ['website_id' => $website->id, 'user_id' => $user->id]);

        $this->assertDatabaseHas('subscriptions', [
            'website_id' => $website->id,
            'user_id' => $user->id
        ]);

        $response = $this->postJson('/api/subscribe', ['website_id' => $website->id, 'user_id' => $user->id]);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Already subscribed'
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'website_id' => $website->id,
            'user_id' => $user->id
        ]);
    }
}
