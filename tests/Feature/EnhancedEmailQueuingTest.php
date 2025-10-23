<?php

namespace Tests\Feature;

use App\Jobs\SendIndividualEmailJob;
use App\Jobs\SendPostNotificationJob;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Website;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EnhancedEmailQueuingTest extends TestCase
{
    use RefreshDatabase;

    public function test_individual_email_job_has_retry_configuration(): void
    {
        $post = new Post(['title' => 'Test Post', 'body' => 'Test Body', 'status' => 'published']);
        $subscriber = new User(['email' => 'test@example.com']);
        $websiteId = 1;

        $job = new SendIndividualEmailJob($post, $subscriber, $websiteId);

        $this->assertEquals(3, $job->tries);
        $this->assertEquals([30, 60, 120], $job->backoff);
        $this->assertEquals(60, $job->timeout);
    }

    public function test_individual_email_job_implements_should_queue(): void
    {
        $post = new Post(['title' => 'Test Post', 'body' => 'Test Body', 'status' => 'published']);
        $subscriber = new User(['email' => 'test@example.com']);
        $websiteId = 1;

        $job = new SendIndividualEmailJob($post, $subscriber, $websiteId);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_main_notification_job_has_retry_configuration(): void
    {
        $post = new Post(['title' => 'Test Post', 'body' => 'Test Body', 'status' => 'published']);
        $websiteId = 1;

        $job = new SendPostNotificationJob($post, $websiteId);

        // Assert retry configuration
        $this->assertEquals(3, $job->tries);
        $this->assertEquals(120, $job->timeout);
    }

    public function test_individual_email_job_contains_correct_data(): void
    {
        $post = new Post(['title' => 'Test Post', 'body' => 'Test Body', 'status' => 'published']);
        $subscriber = new User(['email' => 'test@example.com']);
        $websiteId = 123;

        $job = new SendIndividualEmailJob($post, $subscriber, $websiteId);

        $this->assertEquals($post->title, $job->post->title);
        $this->assertEquals($subscriber->email, $job->subscriber->email);
        $this->assertEquals($websiteId, $job->websiteId);
    }

    public function test_send_post_notification_job_queues_individual_emails(): void
    {
        Queue::fake();

        $websiteOwner = User::factory()->create();
        $subscriber1 = User::factory()->create(['email' => 'subscriber1@example.com']);
        $subscriber2 = User::factory()->create(['email' => 'subscriber2@example.com']);

        $website = Website::factory()->create(['user_id' => $websiteOwner->id]);

        Subscription::create(['website_id' => $website->id, 'user_id' => $subscriber1->id]);
        Subscription::create(['website_id' => $website->id, 'user_id' => $subscriber2->id]);

        // Create a published post
        $post = Post::factory()->published()->create(['website_id' => $website->id]);

        // Dispatch the main notification job directly
        $job = new SendPostNotificationJob($post, $website->id);
        $job->handle(); // Manually execute the job to dispatch individual email jobs

        // Assert that individual email jobs were queued for each subscriber
        Queue::assertPushed(SendIndividualEmailJob::class, 2);

        // Verify each subscriber has a job
        Queue::assertPushed(SendIndividualEmailJob::class, function ($job) use ($subscriber1) {
            return $job->subscriber->email === $subscriber1->email;
        });

        Queue::assertPushed(SendIndividualEmailJob::class, function ($job) use ($subscriber2) {
            return $job->subscriber->email === $subscriber2->email;
        });
    }

    public function test_no_individual_jobs_queued_when_no_subscribers(): void
    {
        Queue::fake();

        $websiteOwner = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $websiteOwner->id]);
        $post = Post::factory()->published()->create(['website_id' => $website->id]);

        // Dispatch the main notification job directly
        $job = new SendPostNotificationJob($post, $website->id);
        $job->handle(); // Manually execute the job

        // Assert that no individual email jobs were queued
        Queue::assertNotPushed(SendIndividualEmailJob::class);
    }
}
