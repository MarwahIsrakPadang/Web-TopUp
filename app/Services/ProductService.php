<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $repository
    ) {}

    public function paginate(?int $gameId = null, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($gameId, $search, $perPage);
    }

    public function findByIdOrFail(int $id): Product
    {
        return $this->repository->findByIdOrFail($id);
    }

    public function create(array $data, ?UploadedFile $icon = null): Product
    {
        if ($icon) {
            $data['icon'] = ImageHelper::upload($icon, 'products');
        }

        return $this->repository->create($data);
    }

    public function update(Product $product, array $data, ?UploadedFile $icon = null): void
    {
        $data['icon'] = ImageHelper::replace($icon, $product->icon, 'products');

        $this->repository->update($product, $data);
    }

    public function delete(Product $product): void
    {
        ImageHelper::delete($product->icon);

        $this->repository->delete($product);
    }
}
