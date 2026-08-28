<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GameRequest;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function __construct(
        private readonly GameService $gameService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Games/Index', [
            'games' => $this->gameService->paginate(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Games/Form', [
            'game' => null,
        ]);
    }

    public function store(GameRequest $request): RedirectResponse
    {
        $this->gameService->create(
            $request->safe()->except(['icon']),
            $request->file('icon')
        );

        return redirect()
            ->route('admin.games.index')
            ->with('success', 'Game berhasil ditambahkan.');
    }

    public function edit(Game $game): Response
    {
        return Inertia::render('Admin/Games/Form', [
            'game' => $game,
        ]);
    }

    public function update(GameRequest $request, Game $game): RedirectResponse
    {
        $this->gameService->update(
            $game,
            $request->safe()->except(['icon']),
            $request->file('icon')
        );

        return redirect()
            ->route('admin.games.index')
            ->with('success', 'Game berhasil diperbarui.');
    }

    public function destroy(Game $game): RedirectResponse
    {
        $this->gameService->delete($game);

        return redirect()
            ->route('admin.games.index')
            ->with('success', 'Game berhasil dihapus.');
    }
}
