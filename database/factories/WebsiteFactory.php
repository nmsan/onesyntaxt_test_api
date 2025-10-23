<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Website>
 */
class WebsiteFactory extends Factory
{

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'url' => fake()->unique()->slug(),
            'user_id' => fake()->numberBetween(1, 10),
        ];
    }


}
