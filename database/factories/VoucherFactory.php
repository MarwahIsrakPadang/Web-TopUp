<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('??????')),
            'type' => 'percentage',
            'amount' => 10,
            'minimum_order' => 0,
            'maximum_usage' => null,
            'used_count' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'status' => 'active',
        ];
    }
}
