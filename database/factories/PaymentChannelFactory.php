<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentChannelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'payment_method_id' => PaymentMethod::factory(),
            'name' => 'QRIS',
            'code' => 'QRIS',
            'minimum_amount' => null,
            'maximum_amount' => null,
            'fee_type' => 'fixed',
            'fee_amount' => 0,
            'status' => 'active',
        ];
    }
}
