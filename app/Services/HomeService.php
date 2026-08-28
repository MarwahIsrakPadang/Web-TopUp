<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Game;
use App\Models\News;
use Illuminate\Support\Facades\Cache;

class HomeService
{
    public function getLandingData(): array
    {
        $banners = Cache::remember('home_banners', 3600, fn() =>
            Banner::active()->orderBy('sort_order')->get()
        );

        $games = Cache::remember('home_games', 3600, fn() =>
            Game::active()
                ->withCount('products')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
        );

        $news = Cache::remember('home_news', 3600, fn() =>
            News::published()
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->get()
        );

        return compact('banners', 'games', 'news');
    }
}
