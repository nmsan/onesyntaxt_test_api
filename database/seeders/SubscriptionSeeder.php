<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $websites = Website::all();
        foreach ($users as $user) {
            $subscriptionCount = rand(3, 7);
            $randomWebsites = $websites->random($subscriptionCount);
            foreach ($randomWebsites as $website) {
                Subscription::firstOrCreate([
                    'user_id' => $user->id,
                    'website_id' => $website->id,
                ]);
            }
        }

        $this->command->info('Subscriptions seeded successfully!');
        $this->command->info('Created ' . Subscription::count() . ' subscriptions');
    }
}
