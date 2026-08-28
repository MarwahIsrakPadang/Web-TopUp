<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Mobile Legends';

        return [
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'icon' => null,
            'description' => $this->faker->sentence(),
            'status' => 'active',
            'sort_order' => 0,
        ];
    }
}
