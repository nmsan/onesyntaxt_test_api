<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_post(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/post/' . $website->id,
            ['title' => 'Test Post', 'body' => 'Test Body', 'status' => 'draft']
        );

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Post created successfully'
        ]);

        $this->assertDatabaseHas('posts', ['title' => 'Test Post', 'body' => 'Test Body', 'status' => 'draft']);
    }

    public function test_user_can_create_post_with_published_status(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/post/' . $website->id,
            ['title' => 'Published Post', 'body' => 'This is a published post', 'status' => 'published']
        );

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Post created successfully'
        ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Published Post',
            'body' => 'This is a published post',
            'status' => 'published',
            'website_id' => $website->id
        ]);
    }


    public function test_post_creation_requires_title(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/post/' . $website->id,
            ['body' => 'Test Body', 'status' => 'draft']
        );

        $response->assertStatus(422);
    }

    public function test_post_creation_requires_body(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/post/' . $website->id,
            ['title' => 'Test Post', 'status' => 'draft']
        );

        $response->assertStatus(422);
    }

    public function test_post_creation_with_invalid_website_id(): void
    {
        $response = $this->postJson('/api/post/999999',
            ['title' => 'Test Post', 'body' => 'Test Body', 'status' => 'draft']
        );

        $response->assertStatus(404);
    }

    public function test_post_creation_with_empty_data(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/post/' . $website->id, []);

        $response->assertStatus(422);
    }

    public function test_post_creation_with_long_title(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $longTitle = str_repeat('a', 300); // Very long title

        $response = $this->postJson('/api/post/' . $website->id,
            ['title' => $longTitle, 'body' => 'Test Body', 'status' => 'draft']
        );

        $response->assertStatus(422);
    }

    public function test_user_can_update_a_post(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $post = Post::create([
            'title' => 'Original Title',
            'body' => 'Original Body',
            'status' => 'draft',
            'website_id' => $website->id
        ]);

        $response = $this->putJson('/api/post/' . $post->id, [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
            'status' => 'published'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Post updated successfully'
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'body' => 'Updated Body',
            'status' => 'published'
        ]);
    }

    public function test_post_update_requires_title(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $post = Post::create([
            'title' => 'Original Title',
            'body' => 'Original Body',
            'status' => 'draft',
            'website_id' => $website->id
        ]);

        $response = $this->putJson('/api/post/' . $post->id, [
            'body' => 'Updated Body',
            'status' => 'published'
        ]);

        $response->assertStatus(422);
    }

    public function test_post_update_requires_body(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $post = Post::create([
            'title' => 'Original Title',
            'body' => 'Original Body',
            'status' => 'draft',
            'website_id' => $website->id
        ]);

        $response = $this->putJson('/api/post/' . $post->id, [
            'title' => 'Updated Title',
            'status' => 'published'
        ]);

        $response->assertStatus(422);
    }

    public function test_post_update_with_invalid_post_id(): void
    {
        $response = $this->putJson('/api/post/999999', [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
            'status' => 'published'
        ]);

        $response->assertStatus(404);
    }

    public function test_post_update_with_empty_data(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $post = Post::create([
            'title' => 'Original Title',
            'body' => 'Original Body',
            'status' => 'draft',
            'website_id' => $website->id
        ]);

        $response = $this->putJson('/api/post/' . $post->id, []);

        $response->assertStatus(422);
    }
}
