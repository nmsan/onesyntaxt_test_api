<?php

namespace App\Jobs;

use App\Mail\PostNotificationMail;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendIndividualEmailJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120];
    public $timeout = 60;

    public Post $post;
    public User $subscriber;
    public int $websiteId;

    public function __construct(Post $post, User $subscriber, int $websiteId)
    {
        $this->post = $post;
        $this->subscriber = $subscriber;
        $this->websiteId = $websiteId;
    }

    public function handle(): void
    {
        try {
            Mail::to($this->subscriber->email)->send(new PostNotificationMail($this->post));
            Log::info("Email sent successfully to subscriber: {$this->subscriber->email} for post: {$this->post->title}");

        } catch (\Exception $e) {
            Log::error("Failed to send email to subscriber: {$this->subscriber->email} for post: {$this->post->title}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Email job failed permanently for subscriber: {$this->subscriber->email} for post: {$this->post->title}. Error: " . $exception->getMessage());
    }

    public function backoff(): array
    {
        return $this->backoff;
    }
}
