<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageRequired = $this->route('banner') ? 'nullable' : 'required';

        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => [$imageRequired, 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'link' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::enum(StatusEnum::class)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Judul',
            'subtitle' => 'Subjudul',
            'image' => 'Gambar',
            'link' => 'Tautan',
            'status' => 'Status',
            'sort_order' => 'Urutan',
        ];
    }
}
