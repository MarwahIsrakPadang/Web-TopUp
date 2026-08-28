<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use App\Services\BannerService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BannerController extends Controller
{
    public function __construct(
        private readonly BannerService $bannerService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Banners/Index', [
            'banners' => $this->bannerService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Banners/Form', [
            'banner' => null,
        ]);
    }

    public function store(BannerRequest $request): RedirectResponse
    {
        $this->bannerService->create(
            $request->safe()->except('image'),
            $request->file('image')
        );

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil ditambahkan.');
    }

    public function edit(Banner $banner): Response
    {
        return Inertia::render('Admin/Banners/Form', [
            'banner' => $banner,
        ]);
    }

    public function update(BannerRequest $request, Banner $banner): RedirectResponse
    {
        $this->bannerService->update(
            $banner,
            $request->safe()->except('image'),
            $request->file('image')
        );

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->bannerService->delete($banner);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil dihapus.');
    }
}
