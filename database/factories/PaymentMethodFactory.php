<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'QRIS',
            'code' => 'qris',
            'icon' => null,
            'status' => 'active',
            'sort_order' => 0,
        ];
    }
}
