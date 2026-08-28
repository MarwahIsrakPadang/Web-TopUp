<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Repositories\PaymentChannelRepository;
use App\Repositories\PaymentMethodRepository;
use Illuminate\Database\Eloquent\Collection;

class PaymentMethodService
{
    public function __construct(
        private readonly PaymentMethodRepository $methodRepository,
        private readonly PaymentChannelRepository $channelRepository,
    ) {}

    public function getAllWithChannels(): Collection
    {
        return $this->methodRepository->getAllWithChannels();
    }

    public function findMethodByIdOrFail(int $id): PaymentMethod
    {
        return $this->methodRepository->findByIdOrFail($id);
    }

    public function createMethod(array $data): PaymentMethod
    {
        return $this->methodRepository->create($data);
    }

    public function updateMethod(PaymentMethod $method, array $data): void
    {
        $this->methodRepository->update($method, $data);
    }

    public function deleteMethod(PaymentMethod $method): void
    {
        $this->methodRepository->delete($method);
    }

    public function findChannelByIdOrFail(int $id): \App\Models\PaymentChannel
    {
        return $this->channelRepository->findByIdOrFail($id);
    }

    public function createChannel(array $data): \App\Models\PaymentChannel
    {
        return $this->channelRepository->create($data);
    }

    public function updateChannel(\App\Models\PaymentChannel $channel, array $data): void
    {
        $this->channelRepository->update($channel, $data);
    }

    public function deleteChannel(\App\Models\PaymentChannel $channel): void
    {
        $this->channelRepository->delete($channel);
    }
}
