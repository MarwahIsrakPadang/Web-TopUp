<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Game;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    private function getGamesWithCategories()
    {
        return Game::select('id', 'name')
            ->with('categories:id,game_id,name')
            ->orderBy('name')
            ->get();
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => $this->productService->paginate(
                $request->integer('game_id') ?: null,
                $request->string('search') ?: null
            ),
            'games' => Game::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => null,
            'games' => $this->getGamesWithCategories(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $this->productService->create(
            $request->safe()->except(['icon']),
            $request->file('icon')
        );

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): Response
    {
        $product->load(['game', 'category']);

        return Inertia::render('Admin/Products/Form', [
            'product' => $product,
            'games' => $this->getGamesWithCategories(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update(
            $product,
            $request->safe()->except(['icon']),
            $request->file('icon')
        );

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
