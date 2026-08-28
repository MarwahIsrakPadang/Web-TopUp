<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\Game;
use App\Repositories\GameRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class GameService
{
    public function __construct(
        private readonly GameRepository $repository
    ) {}

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findByIdOrFail(int $id): Game
    {
        return $this->repository->findByIdOrFail($id);
    }

    public function create(array $data, ?UploadedFile $icon = null): Game
    {
        if ($icon) {
            $data['icon'] = ImageHelper::upload($icon, 'games');
        }

        $game = $this->repository->create($data);

        Cache::forget('home_games');

        return $game;
    }

    public function update(Game $game, array $data, ?UploadedFile $icon = null): void
    {
        $data['icon'] = ImageHelper::replace($icon, $game->icon, 'games');

        $this->repository->update($game, $data);

        Cache::forget('home_games');
    }

    public function delete(Game $game): void
    {
        ImageHelper::delete($game->icon);

        $this->repository->delete($game);

        Cache::forget('home_games');
    }
}
