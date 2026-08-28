<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Enums\VoucherTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $voucherId = $this->route('voucher');

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vouchers', 'code')->ignore($voucherId),
            ],
            'type' => ['required', Rule::enum(VoucherTypeEnum::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'minimum_order' => ['nullable', 'numeric', 'min:0'],
            'maximum_usage' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', Rule::enum(StatusEnum::class)],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->validated();

            if (($data['type'] ?? '') === 'percentage' && ($data['amount'] ?? 0) > 100) {
                $validator->errors()->add('amount', 'Diskon persentase tidak boleh lebih dari 100%.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'code' => 'Kode Voucher',
            'type' => 'Tipe Diskon',
            'amount' => 'Jumlah Diskon',
            'minimum_order' => 'Minimal Belanja',
            'maximum_usage' => 'Maksimal Penggunaan',
            'start_date' => 'Tanggal Mulai',
            'end_date' => 'Tanggal Berakhir',
            'status' => 'Status',
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after' => 'Tanggal berakhir harus setelah tanggal mulai.',
        ];
    }
}
