<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\Banner;
use App\Repositories\BannerRepository;
use Illuminate\Http\UploadedFile;

class BannerService
{
    public function __construct(
        private readonly BannerRepository $repository
    ) {}

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getAll();
    }

    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getActive();
    }

    public function findByIdOrFail(int $id): Banner
    {
        return $this->repository->findByIdOrFail($id);
    }

    public function create(array $data, UploadedFile $image): Banner
    {
        $data['image'] = ImageHelper::upload($image, 'banners');

        return $this->repository->create($data);
    }

    public function update(Banner $banner, array $data, ?UploadedFile $image = null): void
    {
        if ($image) {
            $data['image'] = ImageHelper::replace($image, $banner->image, 'banners');
        }

        $this->repository->update($banner, $data);
    }

    public function delete(Banner $banner): void
    {
        ImageHelper::delete($banner->image);
        $this->repository->delete($banner);
    }
}
