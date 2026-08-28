<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ProductRepository
{
    public function paginate(?int $gameId = null, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $cacheKey = 'products_page_' . ($gameId ?: 'all') . "_search_" . ($search ?: 'none') . "_{$perPage}";

        return Cache::remember($cacheKey, 3600, fn() =>
            Product::query()
                ->with(['game:id,name', 'category:id,name'])
                ->when($gameId, fn($q) => $q->where('game_id', $gameId))
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate($perPage)
        );
    }

    public function findById(int $id): ?Product
    {
        return Product::with(['game', 'category'])->find($id);
    }

    public function findByIdOrFail(int $id): Product
    {
        return Product::with(['game', 'category'])->findOrFail($id);
    }

    public function create(array $data): Product
    {
        $product = Product::create($data);

        Cache::forget('products_page_all_10');
        Cache::forget('products_page_' . ($data['game_id'] ?? 'all') . '_10');

        return $product;
    }

    public function update(Product $product, array $data): bool
    {
        $result = $product->update($data);

        Cache::forget('products_page_all_10');
        Cache::forget('products_page_' . ($product->game_id ?? 'all') . '_10');

        return $result;
    }

    public function delete(Product $product): ?bool
    {
        $result = $product->delete();

        Cache::forget('products_page_all_10');
        Cache::forget('products_page_' . ($product->game_id ?? 'all') . '_10');

        return $result;
    }
}