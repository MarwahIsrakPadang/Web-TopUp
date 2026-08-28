<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'game_id' => ['required', 'exists:games,id'],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug')
                    ->where('game_id', $this->input('game_id'))
                    ->ignore($productId),
            ],
            'type' => ['required', Rule::enum(ProductTypeEnum::class)],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0', 'gte:price'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'status' => ['required', Rule::enum(StatusEnum::class)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'game_id' => 'Game',
            'category_id' => 'Kategori',
            'name' => 'Nama Produk',
            'slug' => 'Slug',
            'type' => 'Tipe',
            'price' => 'Harga',
            'original_price' => 'Harga Asli',
            'description' => 'Deskripsi',
            'icon' => 'Ikon',
            'status' => 'Status',
            'sort_order' => 'Urutan',
        ];
    }

    public function messages(): array
    {
        return [
            'original_price.gte' => 'Harga asli harus lebih besar atau sama dengan harga jual.',
        ];
    }
}
