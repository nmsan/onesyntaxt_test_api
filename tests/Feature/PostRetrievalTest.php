<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostRetrievalTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_retrieve_posts_for_existing_website(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        Post::factory()->create([
            'title' => 'First Post',
            'body' => 'This is the first post',
            'status' => 'published',
            'website_id' => $website->id
        ]);

        Post::factory()->create([
            'title' => 'Second Post',
            'body' => 'This is the second post',
            'status' => 'draft',
            'website_id' => $website->id
        ]);

        $response = $this->getJson("/api/website/{$website->id}/posts");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Posts retrieved successfully'
        ]);

        $responseData = $response->json('data');
        $this->assertCount(1, $responseData);

        $this->assertArrayHasKey('id', $responseData[0]);
        $this->assertArrayHasKey('title', $responseData[0]);
        $this->assertArrayHasKey('body', $responseData[0]);
        $this->assertArrayHasKey('status', $responseData[0]);
        $this->assertArrayHasKey('website_id', $responseData[0]);
        $this->assertArrayHasKey('created_at', $responseData[0]);
        $this->assertArrayHasKey('updated_at', $responseData[0]);
        $this->assertArrayHasKey('website', $responseData[0]);

        $this->assertArrayHasKey('id', $responseData[0]['website']);
        $this->assertArrayHasKey('name', $responseData[0]['website']);
        $this->assertArrayHasKey('url', $responseData[0]['website']);
        $this->assertArrayHasKey('user_id', $responseData[0]['website']);
    }

    public function test_can_retrieve_posts_when_website_has_no_posts(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/website/{$website->id}/posts");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Posts retrieved successfully'
        ]);

        $responseData = $response->json('data');
        $this->assertCount(0, $responseData);
        $this->assertIsArray($responseData);
    }

    public function test_returns_404_when_website_does_not_exist(): void
    {
        $response = $this->getJson('/api/website/999999/posts');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Website not found'
        ]);
    }
}
