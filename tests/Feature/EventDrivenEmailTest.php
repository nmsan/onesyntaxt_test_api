<?php

namespace Tests\Feature;

use App\Events\PostPublished;
use App\Jobs\SendPostNotificationJob;
use App\Models\Post;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EventDrivenEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_published_event_is_fired_when_post_is_created(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/post/' . $website->id, [
            'title' => 'Published Post',
            'body' => 'This is a published post',
            'status' => 'published'
        ]);

        $response->assertStatus(200);

        // Assert that the PostPublished event was fired
        Event::assertDispatched(PostPublished::class, function ($event) use ($website) {
            return $event->websiteId === $website->id && $event->post->status === 'published';
        });
    }

    public function test_post_published_event_is_not_fired_for_draft_posts(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/post/' . $website->id, [
            'title' => 'Draft Post',
            'body' => 'This is a draft post',
            'status' => 'draft'
        ]);

        $response->assertStatus(200);

        // Assert that no PostPublished event was fired
        Event::assertNotDispatched(PostPublished::class);
    }

    public function test_post_published_event_is_fired_when_post_status_changes_to_published(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        // Create a draft post first
        $post = Post::create([
            'title' => 'Original Title',
            'body' => 'Original Body',
            'status' => 'draft',
            'website_id' => $website->id
        ]);

        // Update to published status
        $response = $this->putJson('/api/post/' . $post->id, [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
            'status' => 'published'
        ]);

        $response->assertStatus(200);

        // Assert that the PostPublished event was fired
        Event::assertDispatched(PostPublished::class, function ($event) use ($website) {
            return $event->websiteId === $website->id && $event->post->status === 'published';
        });
    }

    public function test_send_email_notification_listener_dispatches_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        $post = Post::factory()->create([
            'status' => 'published',
            'website_id' => $website->id
        ]);

        // Create a synchronous listener for testing
        $listener = new \App\Listeners\SendEmailNotification();
        
        // Fire the PostPublished event and handle it synchronously
        $event = new PostPublished($post, $website->id);
        $listener->handle($event);

        // Assert that the SendPostNotificationJob was dispatched
        Queue::assertPushed(SendPostNotificationJob::class, function ($job) use ($website) {
            return $job->websiteId === $website->id;
        });
    }

    public function test_event_listener_is_queued(): void
    {
        $listener = new \App\Listeners\SendEmailNotification();
        
        // Assert that the listener implements ShouldQueue
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $listener);
    }

    public function test_event_contains_correct_data(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);
        $post = Post::factory()->create([
            'title' => 'Test Post',
            'body' => 'Test Body',
            'status' => 'published',
            'website_id' => $website->id
        ]);

        $event = new PostPublished($post, $website->id);

        $this->assertEquals($post->id, $event->post->id);
        $this->assertEquals($website->id, $event->websiteId);
        $this->assertEquals('Test Post', $event->post->title);
        $this->assertEquals('Test Body', $event->post->body);
    }
}
