<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', Rule::in(['tripay', 'game_api'])],
            'configs' => ['required', 'array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'provider' => 'Provider',
            'configs' => 'Konfigurasi',
        ];
    }
}
