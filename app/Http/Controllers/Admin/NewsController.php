<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsRequest;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function __construct(
        private readonly NewsService $newsService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/News/Index', [
            'newsList' => $this->newsService->paginate(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/News/Form', [
            'news' => null,
        ]);
    }

    public function store(NewsRequest $request): RedirectResponse
    {
        $this->newsService->create(
            $request->safe()->except('thumbnail'),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(News $news): Response
    {
        return Inertia::render('Admin/News/Form', [
            'news' => $news,
        ]);
    }

    public function update(NewsRequest $request, News $news): RedirectResponse
    {
        $this->newsService->update(
            $news,
            $request->safe()->except('thumbnail'),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $this->newsService->delete($news);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Berita berhasil dihapus.');
    }
}
