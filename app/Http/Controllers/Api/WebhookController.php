<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatusEnum;
use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessOrderJob;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private readonly TripayService $tripayService
    ) {}

    public function handle(Request $request)
    {
        $payload = $request->all();

        if (!$this->tripayService->verifyCallbackSignature($payload)) {
            Log::warning('Tripay webhook: Invalid signature', ['payload' => $payload]);
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }

        $merchantRef = $payload['merchant_ref'] ?? null;
        $tripayReference = $payload['reference'] ?? null;
        $tripayStatus = $payload['status'] ?? null;

        if (!$merchantRef || !$tripayReference || !$tripayStatus) {
            Log::error('Tripay webhook: Invalid payload', ['payload' => $payload]);
            return response()->json(['success' => false, 'message' => 'Invalid payload'], 400);
        }

        $statusMap = [
            'PAID' => OrderStatusEnum::Paid,
            'FAILED' => OrderStatusEnum::Failed,
            'EXPIRED' => OrderStatusEnum::Expired,
        ];

        $newStatus = $statusMap[$tripayStatus] ?? null;

        if ($newStatus === null) {
            return response()->json(['success' => false, 'message' => 'Unknown status'], 400);
        }

        [$order, $oldStatus] = DB::transaction(function () use ($merchantRef, $newStatus, $tripayReference, $tripayStatus, $payload) {
            $order = Order::where('invoice_number', $merchantRef)
                ->lockForUpdate()
                ->first();

            if (!$order) {
                Log::warning('Tripay webhook: Order not found', ['merchant_ref' => $merchantRef]);
                return [null, null];
            }

            // Idempotency: hanya transisi dari pending yang diproses.
            // Callback duplikat atau telat (mis. EXPIRED setelah PAID) diabaikan.
            if ($order->status !== OrderStatusEnum::Pending) {
                Log::info('Tripay webhook: Order already processed or not pending', [
                    'merchant_ref' => $merchantRef,
                    'current_status' => $order->status->value
                ]);
                return [null, null];
            }

            $oldStatus = $order->status->value;

            $order->update([
                'status' => $newStatus,
                'payment_reference' => $tripayReference,
                'paid_at' => $tripayStatus === 'PAID' ? now() : $order->paid_at,
            ]);

            Transaction::create([
                'order_id' => $order->id,
                'transaction_reference' => $tripayReference,
                'payment_method' => $order->payment_method_name,
                'payment_channel' => $order->payment_channel_name,
                'amount' => $order->total_amount,
                'status' => $tripayStatus,
                'raw_response' => $payload,
            ]);

            return [$order, $oldStatus];
        });

        if (!$order) {
            return response()->json(['success' => true, 'message' => 'Order not found or already processed']);
        }

        OrderStatusUpdated::dispatch($order, $oldStatus, $newStatus->value);

        if ($newStatus === OrderStatusEnum::Paid) {
            ProcessOrderJob::dispatch($order)->afterCommit();
        }

        return response()->json(['success' => true]);
    }
}
