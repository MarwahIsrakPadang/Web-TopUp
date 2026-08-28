<?php

namespace App\Http\Requests\Admin;

use App\Enums\FeeTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentChannelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $channelId = $this->route('channel');

        return [
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('payment_channels', 'code')
                    ->where('payment_method_id', $this->input('payment_method_id'))
                    ->ignore($channelId),
            ],
            'minimum_amount' => ['nullable', 'numeric', 'min:0'],
            'maximum_amount' => ['nullable', 'numeric', 'min:0', 'gte:minimum_amount'],
            'fee_type' => ['required', Rule::enum(FeeTypeEnum::class)],
            'fee_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(StatusEnum::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'payment_method_id' => 'Metode Pembayaran',
            'name' => 'Nama Channel',
            'code' => 'Kode',
            'minimum_amount' => 'Jumlah Minimal',
            'maximum_amount' => 'Jumlah Maksimal',
            'fee_type' => 'Tipe Biaya',
            'fee_amount' => 'Biaya',
            'status' => 'Status',
        ];
    }
}
