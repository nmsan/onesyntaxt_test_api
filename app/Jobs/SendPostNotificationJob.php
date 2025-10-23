<?php

namespace App\Jobs;

use App\Jobs\SendIndividualEmailJob;
use App\Models\Post;
use App\Models\Subscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPostNotificationJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public Post $post;
    public int $websiteId;

    public function __construct(Post $post, int $websiteId)
    {
        $this->post = $post;
        $this->websiteId = $websiteId;
    }

    public function handle(): void
    {
        try {

            $subscribers = Subscription::with('user')
                ->where('website_id', $this->websiteId)
                ->get();

            if ($subscribers->isEmpty()) {
                Log::info("No subscribers found for website ID: {$this->websiteId}");
                return;
            }

            Log::info("Queuing emails for {$subscribers->count()} subscribers for post: {$this->post->title}");

            // Queue individual email job for each subscriber
            foreach ($subscribers as $subscription) {
                SendIndividualEmailJob::dispatch(
                    $this->post,
                    $subscription->user,
                    $this->websiteId
                );
            }

            Log::info("Successfully queued {$subscribers->count()} individual email jobs for post: {$this->post->title}");

        } catch (\Exception $e) {
            Log::error("Failed to queue email notifications for post: {$this->post->title}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Post notification job failed permanently for post: {$this->post->title}. Error: " . $exception->getMessage());
    }
}
