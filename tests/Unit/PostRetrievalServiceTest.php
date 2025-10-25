<?php

namespace Tests\Unit;

use App\Contracts\PostRetrievalInterface;
use App\Models\Post;
use App\Models\User;
use App\Models\Website;
use App\Services\PostRetrievalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostRetrievalServiceTest extends TestCase
{
    use RefreshDatabase;

    private PostRetrievalService $postRetrievalService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->postRetrievalService = new PostRetrievalService();
    }

    public function test_service_implements_post_retrieval_interface(): void
    {
        $this->assertInstanceOf(PostRetrievalInterface::class, $this->postRetrievalService);
    }

    public function test_get_posts_by_website_returns_collection(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        
        Post::factory()->count(3)->create([
            'website_id' => $website->id,
            'status' => 'published'
        ]);

        // Act
        $result = $this->postRetrievalService->getPostsByWebsite($website->id);

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function test_get_posts_by_website_returns_empty_collection_when_no_posts(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        // Act
        $result = $this->postRetrievalService->getPostsByWebsite($website->id);

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_get_posts_by_website_includes_website_relationship(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create([
            'name' => 'Test Website',
            'user_id' => $user->id
        ]);
        
        $post = Post::factory()->create([
            'website_id' => $website->id,
            'status' => 'published'
        ]);

        // Act
        $result = $this->postRetrievalService->getPostsByWebsite($website->id);

        // Assert
        $this->assertCount(1, $result);
        
        $firstPost = $result->first();
        $this->assertTrue($firstPost->relationLoaded('website'));
        $this->assertEquals('Test Website', $firstPost->website->name);
    }

    public function test_get_posts_by_website_orders_by_created_at_desc(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        
        $oldestPost = Post::factory()->create([
            'title' => 'Oldest',
            'website_id' => $website->id,
            'created_at' => now()->subDays(3),
            'status' => 'published'
        ]);
        
        $newestPost = Post::factory()->create([
            'title' => 'Newest',
            'website_id' => $website->id,
            'created_at' => now()->subDays(1),
            'status' => 'published'
        ]);
        
        $middlePost = Post::factory()->create([
            'title' => 'Middle',
            'website_id' => $website->id,
            'created_at' => now()->subDays(2),
            'status' => 'published'
        ]);

        // Act
        $result = $this->postRetrievalService->getPostsByWebsite($website->id);

        // Assert
        $this->assertCount(3, $result);
        
        $titles = $result->pluck('title')->toArray();
        $this->assertEquals(['Newest', 'Middle', 'Oldest'], $titles);
    }

    public function test_get_posts_by_website_filters_by_website_id(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website1 = Website::factory()->create(['user_id' => $user->id]);
        $website2 = Website::factory()->create(['user_id' => $user->id]);
        
        // Create posts for both websites
        Post::factory()->count(2)->create([
            'website_id' => $website1->id,
            'status' => 'published'
        ]);
        Post::factory()->count(3)->create([
            'website_id' => $website2->id,
            'status' => 'published'
        ]);

        // Act
        $result1 = $this->postRetrievalService->getPostsByWebsite($website1->id);
        $result2 = $this->postRetrievalService->getPostsByWebsite($website2->id);

        // Assert
        $this->assertCount(2, $result1);
        $this->assertCount(3, $result2);
        
        // Verify all posts belong to correct website
        foreach ($result1 as $post) {
            $this->assertEquals($website1->id, $post->website_id);
        }
        
        foreach ($result2 as $post) {
            $this->assertEquals($website2->id, $post->website_id);
        }
    }

    public function test_get_posts_by_website_handles_non_existent_website(): void
    {
        // Act
        $result = $this->postRetrievalService->getPostsByWebsite(999999);

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_service_is_stateless(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        
        Post::factory()->create([
            'website_id' => $website->id,
            'status' => 'published'
        ]);

        // Act - Call the same method multiple times
        $result1 = $this->postRetrievalService->getPostsByWebsite($website->id);
        $result2 = $this->postRetrievalService->getPostsByWebsite($website->id);

        // Assert - Results should be consistent
        $this->assertEquals($result1->count(), $result2->count());
        $this->assertEquals($result1->first()->id, $result2->first()->id);
    }

    public function test_service_performance_with_large_dataset(): void
    {
        // Arrange
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        
        // Create 100 posts
        Post::factory()->count(100)->create([
            'website_id' => $website->id,
            'status' => 'published'
        ]);

        // Act
        $startTime = microtime(true);
        $result = $this->postRetrievalService->getPostsByWebsite($website->id);
        $endTime = microtime(true);

        // Assert
        $this->assertCount(100, $result);
        
        // Performance assertion (should complete in reasonable time)
        $executionTime = $endTime - $startTime;
        $this->assertLessThan(1.0, $executionTime, 'Service should retrieve 100 posts in under 1 second');
    }
}
