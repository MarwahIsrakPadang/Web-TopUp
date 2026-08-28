<?php

namespace App\Repositories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class BannerRepository
{
    public function getAll(): Collection
    {
        return Cache::remember('banners_all', 3600, fn() =>
            Banner::orderBy('sort_order')->orderBy('title')->get()
        );
    }

    public function getActive(): Collection
    {
        return Cache::remember('banners_active', 3600, fn() =>
            Banner::where('status', 'active')->orderBy('sort_order')->get()
        );
    }

    public function findById(int $id): ?Banner
    {
        return Banner::find($id);
    }

    public function findByIdOrFail(int $id): Banner
    {
        return Banner::findOrFail($id);
    }

    public function create(array $data): Banner
    {
        $banner = Banner::create($data);

        Cache::forget('banners_all');
        Cache::forget('banners_active');
        Cache::forget('home_banners');

        return $banner;
    }

    public function update(Banner $banner, array $data): bool
    {
        $result = $banner->update($data);

        Cache::forget('banners_all');
        Cache::forget('banners_active');
        Cache::forget('home_banners');

        return $result;
    }

    public function delete(Banner $banner): ?bool
    {
        $result = $banner->delete();

        Cache::forget('banners_all');
        Cache::forget('banners_active');
        Cache::forget('home_banners');

        return $result;
    }
}