<?php

namespace App\Repositories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PaymentMethodRepository
{
    public function getAllWithChannels(): Collection
    {
        return Cache::remember('payment_methods_with_channels', 3600, fn() =>
            PaymentMethod::with(['channels' => fn($q) => $q->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
        );
    }

    public function findById(int $id): ?PaymentMethod
    {
        return PaymentMethod::with('channels')->find($id);
    }

    public function findByIdOrFail(int $id): PaymentMethod
    {
        return PaymentMethod::with('channels')->findOrFail($id);
    }

    public function create(array $data): PaymentMethod
    {
        $method = PaymentMethod::create($data);

        Cache::forget('payment_methods_with_channels');

        return $method;
    }

    public function update(PaymentMethod $method, array $data): bool
    {
        $result = $method->update($data);

        Cache::forget('payment_methods_with_channels');

        return $result;
    }

    public function delete(PaymentMethod $method): ?bool
    {
        $result = $method->delete();

        Cache::forget('payment_methods_with_channels');

        return $result;
    }
}