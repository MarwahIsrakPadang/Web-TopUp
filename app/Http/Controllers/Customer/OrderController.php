<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    public function index(): Response
    {
        if (auth()->check()) {
            $orders = $this->orderService->getByUser(auth()->id());
        } else {
            $orders = $this->orderService->getBySession();
        }

        return Inertia::render('Public/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function tracking(Request $request): Response
    {
        $phone = $request->query('phone');
        $orders = null;

        if ($phone) {
            $request->validate(['phone' => 'required|string|max:20']);
            $orders = $this->orderService->getByPhone($phone);
        }

        return Inertia::render('Public/Orders/Tracking', [
            'orders' => $orders,
            'phone' => $phone,
        ]);
    }
}
