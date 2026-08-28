<?php

namespace App\Repositories;

use App\Models\Game;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class GameRepository
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Cache::remember("games_page_{$perPage}", 3600, fn() =>
            Game::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate($perPage)
        );
    }

    public function findById(int $id): ?Game
    {
        return Game::find($id);
    }

    public function findByIdOrFail(int $id): Game
    {
        return Game::findOrFail($id);
    }

    public function create(array $data): Game
    {
        $game = Game::create($data);

        Cache::forget('home_games');

        return $game;
    }

    public function update(Game $game, array $data): bool
    {
        $result = $game->update($data);

        Cache::forget('home_games');

        return $result;
    }

    public function delete(Game $game): ?bool
    {
        $result = $game->delete();

        Cache::forget('home_games');

        return $result;
    }
}