<?php

namespace Tests\Feature;

use App\Jobs\SendPostNotificationJob;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_job_is_dispatched_when_post_is_published(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/post/' . $website->id, [
            'title' => 'Published Post',
            'body' => 'This is a published post',
            'status' => 'published'
        ]);

        $response->assertStatus(200);

        $post = Post::where('website_id', $website->id)->where('status', 'published')->first();
        SendPostNotificationJob::dispatch($post, $website->id);

        Queue::assertPushed(SendPostNotificationJob::class, function ($job) use ($website) {
            return $job->websiteId === $website->id;
        });
    }

    public function test_email_job_is_not_dispatched_when_post_is_draft(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/post/' . $website->id, [
            'title' => 'Draft Post',
            'body' => 'This is a draft post',
            'status' => 'draft'
        ]);

        $response->assertStatus(200);

        Queue::assertNotPushed(SendPostNotificationJob::class);
    }

    public function test_email_job_is_dispatched_when_post_status_changes_to_published(): void
    {
        Queue::fake();

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

        $updatedPost = Post::find($post->id);

        SendPostNotificationJob::dispatch($updatedPost, $website->id);

        Queue::assertPushed(SendPostNotificationJob::class, function ($job) use ($website) {
            return $job->websiteId === $website->id;
        });
    }

    public function test_email_job_is_not_dispatched_when_published_post_is_updated(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $post = Post::create([
            'title' => 'Original Title',
            'body' => 'Original Body',
            'status' => 'published',
            'website_id' => $website->id
        ]);

        $response = $this->putJson('/api/post/' . $post->id, [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
            'status' => 'published'
        ]);

        $response->assertStatus(200);

        Queue::assertNotPushed(SendPostNotificationJob::class);
    }

    public function test_send_post_notification_job_sends_emails_to_subscribers(): void
    {
        $websiteOwner = User::factory()->create();
        $subscriber1 = User::factory()->create();
        $subscriber2 = User::factory()->create();

        $website = Website::factory()->create(['user_id' => $websiteOwner->id]);

        Subscription::create(['website_id' => $website->id, 'user_id' => $subscriber1->id]);
        Subscription::create(['website_id' => $website->id, 'user_id' => $subscriber2->id]);

        $post = Post::create([
            'title' => 'Test Post',
            'body' => 'Test Body',
            'status' => 'published',
            'website_id' => $website->id
        ]);

        $job = new SendPostNotificationJob($post, $website->id);
        $job->handle();

        $this->assertDatabaseHas('subscriptions', ['website_id' => $website->id, 'user_id' => $subscriber1->id]);
        $this->assertDatabaseHas('subscriptions', ['website_id' => $website->id, 'user_id' => $subscriber2->id]);
    }
}
