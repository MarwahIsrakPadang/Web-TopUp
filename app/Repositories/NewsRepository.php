<?php

namespace App\Repositories;

use App\Models\News;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class NewsRepository
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Cache::remember("news_page_{$perPage}", 3600, fn() =>
            News::orderBy('created_at', 'desc')->paginate($perPage)
        );
    }

    public function findById(int $id): ?News
    {
        return News::find($id);
    }

    public function findByIdOrFail(int $id): News
    {
        return News::findOrFail($id);
    }

    public function create(array $data): News
    {
        $news = News::create($data);

        Cache::forget('news_page_10');
        Cache::forget('home_news');

        return $news;
    }

    public function update(News $news, array $data): bool
    {
        $result = $news->update($data);

        Cache::forget('news_page_10');
        Cache::forget('home_news');

        return $result;
    }

    public function delete(News $news): ?bool
    {
        $result = $news->delete();

        Cache::forget('news_page_10');
        Cache::forget('home_news');

        return $result;
    }
}