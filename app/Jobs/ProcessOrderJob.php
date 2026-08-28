<?php

namespace App\Jobs;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessOrderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order
    ) {}

    public function handle(): void
    {
        if ($this->order->status !== OrderStatusEnum::Paid) {
            return;
        }

        $this->order->update([
            'status' => OrderStatusEnum::Processing,
        ]);

        // TODO: Kirim notifikasi ke customer (email/WA)
        // TODO: Proses top-up game via API provider
    }
}
