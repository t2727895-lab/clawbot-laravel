<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\ClawbotService;
use Illuminate\Console\Command;

class PublishDuePosts extends Command
{
    protected $signature = 'posts:publish-due';

    protected $description = 'Publish all approved (queued) posts whose scheduled time has arrived';

    public function handle()
    {
        $duePosts = Post::dueForPublishing()->with('platforms')->get();

        if ($duePosts->isEmpty()) {
            $this->info('No posts due for publishing.');
            return;
        }

        $service = new ClawbotService();

        foreach ($duePosts as $post) {
            $this->info("Publishing post #{$post->id}...");
            $result = $service->publish($post);
            $this->info($result['message']);
        }
    }
}