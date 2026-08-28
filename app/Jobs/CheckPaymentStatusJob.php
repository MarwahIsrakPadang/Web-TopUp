<?php

namespace App\Jobs;

use App\Enums\OrderStatusEnum;
use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\TripayService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckPaymentStatusJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order
    ) {}

    public function handle(): void
    {
        if ($this->order->status !== OrderStatusEnum::Pending) {
            return;
        }

        if (empty($this->order->payment_reference)) {
            return;
        }

        try {
            $tripay = app(TripayService::class);

            if (!$tripay->isConfigured()) {
                return;
            }

            $detail = $tripay->getDetailTransaction($this->order->payment_reference);

            $tripayStatus = $detail['status'] ?? null;

            $statusMap = [
                'PAID' => OrderStatusEnum::Paid,
                'FAILED' => OrderStatusEnum::Failed,
                'EXPIRED' => OrderStatusEnum::Expired,
            ];

            $newStatus = $statusMap[$tripayStatus] ?? null;

            if ($newStatus === null) {
                return;
            }

            $oldStatus = $this->order->status->value;

            $this->order->update([
                'status' => $newStatus,
                'paid_at' => $tripayStatus === 'PAID' ? now() : $this->order->paid_at,
            ]);

            Transaction::create([
                'order_id' => $this->order->id,
                'transaction_reference' => $this->order->payment_reference,
                'payment_method' => $this->order->payment_method_name,
                'payment_channel' => $this->order->payment_channel_name,
                'amount' => $this->order->total_amount,
                'status' => $tripayStatus,
                'raw_response' => $detail,
            ]);

            OrderStatusUpdated::dispatch($this->order, $oldStatus, $newStatus->value);

            if ($newStatus === OrderStatusEnum::Paid) {
                ProcessOrderJob::dispatch($this->order);
            }
        } catch (\Exception $e) {
            logger()->error('CheckPaymentStatusJob gagal: ' . $e->getMessage(), [
                'order_id' => $this->order->id,
            ]);
        }
    }
}
