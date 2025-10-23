<?php

namespace Database\Seeders;

use Database\Factories\WebsiteFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WebsiteFactory::times(10)->create();
    }
}
