<?php

namespace Tests\Integration;

use App\Contracts\PostRetrievalInterface;
use App\Models\Post;
use App\Models\User;
use App\Models\Website;
use App\Services\PostRetrievalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostRetrievalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_post_retrieval_flow(): void
    {
        // Arrange - Create complete data structure
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $website = Website::factory()->create([
            'name' => 'Tech Blog',
            'url' => 'https://techblog.example.com',
            'user_id' => $user->id
        ]);
        
        $posts = Post::factory()->count(5)->create([
            'website_id' => $website->id
        ]);

        // Act - Make HTTP request
        $response = $this->getJson("/api/website/{$website->id}/posts");

        // Assert - Verify complete response structure
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'body',
                    'status',
                    'website_id',
                    'created_at',
                    'updated_at',
                    'website' => [
                        'id',
                        'name',
                        'url',
                        'user_id',
                        'created_at',
                        'updated_at',
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'email_verified_at',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ]
        ]);

        $responseData = $response->json('data');
        $this->assertCount(5, $responseData);

        // Verify all posts belong to the correct website
        foreach ($responseData as $postData) {
            $this->assertEquals($website->id, $postData['website_id']);
            $this->assertEquals($website->id, $postData['website']['id']);
            $this->assertEquals('Tech Blog', $postData['website']['name']);
            $this->assertEquals($user->id, $postData['website']['user_id']);
            $this->assertEquals('John Doe', $postData['website']['user']['name']);
        }
    }

    public function test_service_injection_works_correctly(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        
        Post::factory()->create(['website_id' => $website->id]);

        // Act
        $response = $this->getJson("/api/website/{$website->id}/posts");

        // Assert
        $response->assertStatus(200);
        
        // Verify that the service is properly injected and working
        $this->assertInstanceOf(PostRetrievalInterface::class, app(PostRetrievalInterface::class));
        $this->assertInstanceOf(PostRetrievalService::class, app(PostRetrievalInterface::class));
    }

    public function test_database_transactions_are_handled_correctly(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        
        // Create posts in a transaction-like scenario
        Post::factory()->count(3)->create(['website_id' => $website->id]);

        // Act
        $response = $this->getJson("/api/website/{$website->id}/posts");

        // Assert
        $response->assertStatus(200);
        $responseData = $response->json('data');
        $this->assertCount(3, $responseData);

        // Verify database state is consistent
        $this->assertDatabaseCount('posts', 3);
        $this->assertDatabaseHas('posts', ['website_id' => $website->id]);
    }

    public function test_error_handling_integration(): void
    {
        // Test with non-existent website
        $response = $this->getJson('/api/website/999999/posts');
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Website not found']);

        // Test with invalid website ID format
        $response = $this->getJson('/api/website/invalid/posts');
        $response->assertStatus(404); // Laravel route model binding returns 404 for invalid IDs
    }

    public function test_concurrent_requests_handling(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        
        Post::factory()->count(10)->create(['website_id' => $website->id]);

        // Act - Simulate concurrent requests
        $responses = [];
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->getJson("/api/website/{$website->id}/posts");
        }

        // Assert - All responses should be successful and consistent
        foreach ($responses as $response) {
            $response->assertStatus(200);
            $this->assertCount(10, $response->json('data'));
        }
    }

    public function test_memory_usage_with_large_dataset(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        
        // Create 1000 posts
        Post::factory()->count(1000)->create(['website_id' => $website->id]);

        // Act
        $response = $this->getJson("/api/website/{$website->id}/posts");

        // Assert
        $response->assertStatus(200);
        $responseData = $response->json('data');
        $this->assertCount(1000, $responseData);

        // Verify memory usage is reasonable (this is more of a smoke test)
        $memoryUsage = memory_get_peak_usage(true);
        $this->assertLessThan(100 * 1024 * 1024, $memoryUsage, 'Memory usage should be under 100MB for 1000 posts');
    }

    public function test_response_time_performance(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        
        Post::factory()->count(100)->create(['website_id' => $website->id]);

        // Act
        $startTime = microtime(true);
        $response = $this->getJson("/api/website/{$website->id}/posts");
        $endTime = microtime(true);

        // Assert
        $response->assertStatus(200);
        
        $responseTime = $endTime - $startTime;
        $this->assertLessThan(2.0, $responseTime, 'Response time should be under 2 seconds for 100 posts');
    }

    public function test_json_response_format(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        
        Post::factory()->create(['website_id' => $website->id]);

        // Act
        $response = $this->getJson("/api/website/{$website->id}/posts");

        // Assert
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
        
        // Verify JSON is valid
        $jsonData = $response->getContent();
        $this->assertJson($jsonData);
        
        // Verify specific JSON structure
        $response->assertJsonPath('message', 'Posts retrieved successfully');
        $response->assertJsonPath('data.0.website_id', $website->id);
    }
}
