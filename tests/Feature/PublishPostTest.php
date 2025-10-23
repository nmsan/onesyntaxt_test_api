<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_should_able_to_create_a_post(): void
    {
        $adminUser = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $adminUser->id]);

        $this->actingAs($adminUser);

        $response = $this->postJson('/api/post/' . $website->id,
            ['title' => 'Test Post', 'body' => 'Test Body', 'status' => 'draft']
        );

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Post created successfully'
        ]);

        $this->assertDatabaseHas('posts', ['title' => 'Test Post', 'body' => 'Test Body', 'status' => 'draft']);
    }
}
