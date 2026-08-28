<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group' => ['required', 'string', 'in:general,theme,payment'],
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'group' => 'Grup',
            'settings' => 'Pengaturan',
        ];
    }
}
