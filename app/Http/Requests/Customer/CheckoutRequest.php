<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id' => ['required', 'exists:games,id'],
            'product_id' => ['required', 'exists:products,id'],
            'payment_channel_id' => ['required', 'exists:payment_channels,id'],
            'player_id' => ['required', 'string', 'max:255'],
            'player_server' => ['nullable', 'string', 'max:255'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'note' => ['nullable', 'string', 'max:500'],
            'voucher_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'game_id' => 'Game',
            'product_id' => 'Produk',
            'payment_channel_id' => 'Metode Pembayaran',
            'player_id' => 'ID Player',
            'player_server' => 'Server',
            'customer_name' => 'Nama',
            'customer_email' => 'Email',
            'customer_phone' => 'No. Telepon',
            'voucher_code' => 'Kode Voucher',
        ];
    }
}
