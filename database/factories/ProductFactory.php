<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = '86 Diamonds';

        return [
            'game_id' => Game::factory(),
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'type' => 'package',
            'price' => 20000,
            'status' => 'active',
            'sort_order' => 0,
        ];
    }
}
