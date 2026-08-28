<?php

namespace App\Repositories;

use App\Models\PaymentChannel;
use Illuminate\Database\Eloquent\Collection;

class PaymentChannelRepository
{
    public function findByMethodId(int $methodId): Collection
    {
        return PaymentChannel::where('payment_method_id', $methodId)
            ->orderBy('sort_order')
            ->get();
    }

    public function findById(int $id): ?PaymentChannel
    {
        return PaymentChannel::find($id);
    }

    public function findByIdOrFail(int $id): PaymentChannel
    {
        return PaymentChannel::findOrFail($id);
    }

    public function create(array $data): PaymentChannel
    {
        return PaymentChannel::create($data);
    }

    public function update(PaymentChannel $channel, array $data): bool
    {
        return $channel->update($data);
    }

    public function delete(PaymentChannel $channel): ?bool
    {
        return $channel->delete();
    }
}
