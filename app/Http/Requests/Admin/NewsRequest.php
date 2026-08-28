<?php

namespace App\Http\Requests\Admin;

use App\Enums\NewsStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $newsId = $this->route('news');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('news', 'slug')->ignore($newsId),
            ],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'status' => ['required', Rule::enum(NewsStatusEnum::class)],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Judul',
            'slug' => 'Slug',
            'content' => 'Konten',
            'thumbnail' => 'Gambar Sampul',
            'status' => 'Status',
            'published_at' => 'Tanggal Publikasi',
        ];
    }
}
