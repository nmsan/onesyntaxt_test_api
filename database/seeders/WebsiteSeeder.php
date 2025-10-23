<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        $websites = [
            [
                'name' => 'TechCrunch',
                'url' => 'https://techcrunch.com',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Laravel News',
                'url' => 'https://laravel-news.com',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Dev.to',
                'url' => 'https://dev.to',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Medium Tech',
                'url' => 'https://medium.com/topic/technology',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Hacker News',
                'url' => 'https://news.ycombinator.com',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Stack Overflow Blog',
                'url' => 'https://stackoverflow.blog',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'GitHub Blog',
                'url' => 'https://github.blog',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'CSS-Tricks',
                'url' => 'https://css-tricks.com',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Smashing Magazine',
                'url' => 'https://smashingmagazine.com',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'A List Apart',
                'url' => 'https://alistapart.com',
                'user_id' => $users->random()->id,
            ],
        ];

        foreach ($websites as $websiteData) {
            Website::firstOrCreate(
                ['url' => $websiteData['url']],
                $websiteData
            );
        }

        Website::factory(15)->create([
            'user_id' => $users->random()->id,
        ]);
    }
}
