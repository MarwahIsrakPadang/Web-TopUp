<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PaymentMethodController extends Controller
{
    public function __construct(
        private readonly PaymentMethodService $paymentMethodService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/PaymentMethods/Index', [
            'methods' => $this->paymentMethodService->getAllWithChannels(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/PaymentMethods/Form', [
            'method' => null,
        ]);
    }

    public function store(PaymentMethodRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('icon');

        if ($request->hasFile('icon')) {
            $data['icon'] = ImageHelper::upload($request->file('icon'), 'payments');
        }

        $this->paymentMethodService->createMethod($data);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentMethod $paymentMethod): Response
    {
        return Inertia::render('Admin/PaymentMethods/Form', [
            'method' => $paymentMethod,
        ]);
    }

    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $data = $request->safe()->except('icon');

        if ($request->hasFile('icon')) {
            $data['icon'] = ImageHelper::replace(
                $request->file('icon'),
                $paymentMethod->icon,
                'payments'
            );
        }

        $this->paymentMethodService->updateMethod($paymentMethod, $data);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        ImageHelper::delete($paymentMethod->icon);
        $this->paymentMethodService->deleteMethod($paymentMethod);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
