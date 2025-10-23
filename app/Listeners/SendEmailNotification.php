<?php

namespace App\Listeners;

use App\Events\PostPublished;
use App\Jobs\SendPostNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public function __construct(){}

    public function handle(PostPublished $event): void
    {
        SendPostNotificationJob::dispatch($event->post, $event->websiteId);
    }
}
