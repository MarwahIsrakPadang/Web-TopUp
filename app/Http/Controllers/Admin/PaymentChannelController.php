<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentChannelRequest;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PaymentChannelController extends Controller
{
    public function __construct(
        private readonly PaymentMethodService $paymentMethodService
    ) {}

    public function create(PaymentMethod $paymentMethod): Response
    {
        return Inertia::render('Admin/PaymentMethods/ChannelForm', [
            'channel' => null,
            'paymentMethod' => $paymentMethod,
        ]);
    }

    public function store(PaymentChannelRequest $request): RedirectResponse
    {
        $this->paymentMethodService->createChannel($request->validated());

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Channel pembayaran berhasil ditambahkan.');
    }

    public function edit(int $channelId): Response
    {
        $channel = $this->paymentMethodService->findChannelByIdOrFail($channelId);

        return Inertia::render('Admin/PaymentMethods/ChannelForm', [
            'channel' => $channel,
            'paymentMethod' => $channel->paymentMethod,
        ]);
    }

    public function update(PaymentChannelRequest $request, int $channelId): RedirectResponse
    {
        $channel = $this->paymentMethodService->findChannelByIdOrFail($channelId);
        $this->paymentMethodService->updateChannel($channel, $request->validated());

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Channel pembayaran berhasil diperbarui.');
    }

    public function destroy(int $channelId): RedirectResponse
    {
        $channel = $this->paymentMethodService->findChannelByIdOrFail($channelId);
        $this->paymentMethodService->deleteChannel($channel);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Channel pembayaran berhasil dihapus.');
    }
}
