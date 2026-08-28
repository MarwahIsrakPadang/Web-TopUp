<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function show(string $slug): Response
    {
        $game = Cache::remember("game_detail_{$slug}", 1800, fn() =>
            Game::where('slug', $slug)
                ->active()
                ->with(['categories' => fn($q) => $q->orderBy('name')])
                ->firstOrFail()
        );

        $products = Cache::remember("game_products_{$slug}", 1800, fn() =>
            $game->products()
                ->active()
                ->with('category')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
        );

        $paymentMethods = PaymentMethod::query()
            ->active()
            ->with(['channels' => fn ($q) => $q->active()->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Public/Games/Show', [
            'game' => $game,
            'products' => $products,
            'paymentMethods' => $paymentMethods,
        ]);
    }
}
