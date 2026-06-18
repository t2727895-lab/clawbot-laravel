<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Platform;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create platforms
        $platformsData = [
            ['name' => 'LinkedIn', 'slug' => 'linkedin', 'is_active' => true],
            ['name' => 'Facebook', 'slug' => 'facebook', 'is_active' => true],
            ['name' => 'Twitter', 'slug' => 'twitter', 'is_active' => true],
            ['name' => 'Instagram', 'slug' => 'instagram', 'is_active' => false],
        ];

        foreach ($platformsData as $data) {
            Platform::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // Get active platforms
        $activePlatforms = Platform::where('is_active', true)->pluck('id')->toArray();

        // Create sample posts
        $posts = [
            [
                'content' => 'Excited to announce our new product launch! 🚀 This innovative solution will transform how businesses manage their social media presence.',
                'image' => 'https://via.placeholder.com/600x400?text=Product+Launch',
                'status' => 'published',
            ],
            [
                'content' => 'Join us for a webinar next week where we\'ll discuss best practices for social media management. Don\'t miss out! 📅',
                'image' => 'https://via.placeholder.com/600x400?text=Webinar',
                'status' => 'approved',
            ],
            [
                'content' => 'Thank you to our amazing community for reaching 10K followers! Here\'s to growing together. 🙌',
                'image' => null,
                'status' => 'queued',
            ],
        ];

        foreach ($posts as $postData) {
            $post = Post::create($postData);

            // Attach random active platforms
            $selectedPlatforms = array_slice($activePlatforms, 0, rand(1, count($activePlatforms)));
            $post->platforms()->attach($selectedPlatforms);
        }
    }
}
