<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CheckoutRequest;
use App\Models\Game;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkoutService
    ) {}

    public function create(string $slug, int $productId): Response
    {
        $game = Game::where('slug', $slug)->active()->firstOrFail();
        $product = Product::where('id', $productId)->active()->firstOrFail();

        return Inertia::render('Public/Checkout/Index',
            $this->checkoutService->getCheckoutData($game, $product)
        );
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        try {
            $result = $this->checkoutService->process($request->validated());

            return redirect()
                ->route('invoice.show', $result['order']->invoice_number)
                ->with('success', 'Pesanan berhasil dibuat.')
                ->with('tripay', $result['tripay']);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}
