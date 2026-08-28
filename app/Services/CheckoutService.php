<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Enums\VoucherTypeEnum;
use App\Models\Game;
use App\Models\Order;
use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function getCheckoutData(Game $game, Product $product): array
    {
        $paymentMethods = \App\Models\PaymentMethod::active()
            ->with(['channels' => fn($q) => $q->active()->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return [
            'game' => $game->loadCount('products'),
            'product' => $product->load('game'),
            'paymentMethods' => $paymentMethods,
        ];
    }

    public function process(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            $game = $product->game;
            $channel = PaymentChannel::with('paymentMethod')->findOrFail($data['payment_channel_id']);

            $amount = $product->price;
            $discountAmount = 0;
            $voucherId = null;

            if (!empty($data['voucher_code'])) {
                $voucher = Voucher::where('code', strtoupper($data['voucher_code']))
                    ->lockForUpdate()
                    ->first();

                if (!$voucher || !$voucher->isValid()) {
                    throw new \Exception('Voucher tidak valid atau sudah kedaluwarsa.');
                }
                if ($amount < $voucher->minimum_order) {
                    throw new \Exception('Minimal belanja Rp ' . number_format($voucher->minimum_order, 0, ',', '.') . ' untuk menggunakan voucher ini.');
                }

                // Klaim usage secara atomik agar race condition tidak melewati maximum_usage.
                $claimed = Voucher::where('id', $voucher->id)
                    ->where(function ($query) {
                        $query->whereNull('maximum_usage')
                            ->orWhereColumn('used_count', '<', 'maximum_usage');
                    })
                    ->increment('used_count');

                if ($claimed === 0) {
                    throw new \Exception('Kuota voucher ini sudah habis.');
                }

                $voucherId = $voucher->id;

                $discountAmount = $voucher->type === VoucherTypeEnum::Percentage
                    ? round($amount * (float) $voucher->amount / 100)
                    : min((float) $voucher->amount, $amount);
            }

            if ($channel->fee_type === 'percentage') {
                $fee = round($amount * $channel->fee_amount / 100);
            } else {
                $fee = $channel->fee_amount;
            }

            $totalAmount = $amount + $fee - $discountAmount;

            $invoiceNumber = $this->generateInvoiceNumber();

            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => auth()->id(),
                'game_id' => $game->id,
                'game_name' => $game->name,
                'game_icon' => $game->icon,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'player_id' => $data['player_id'],
                'player_server' => $data['player_server'] ?? null,
                'note' => $data['note'] ?? null,
                'amount' => $amount,
                'fee' => $fee,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'voucher_id' => $voucherId,
                'status' => OrderStatusEnum::Pending,
                'payment_method_id' => $channel->paymentMethod->id,
                'payment_method_name' => $channel->paymentMethod->name,
                'payment_channel_id' => $channel->id,
                'payment_channel_name' => $channel->name,
            ]);

            session()->push('order_ids', $order->id);

            $tripayResult = null;
            try {
                $tripayService = app(TripayService::class);

                if ($tripayService->isConfigured()) {
                    $customerData = [
                        'name' => $data['customer_name'] ?? 'Guest',
                        'email' => $data['customer_email'] ?? '',
                        'phone' => $data['customer_phone'] ?? '',
                    ];

                    $tripayResult = $tripayService->createTransaction($order, $customerData);

                    $order->update([
                        'payment_reference' => $tripayResult['reference'],
                    ]);

                    $order->transactions()->create([
                        'transaction_reference' => $tripayResult['reference'],
                        'payment_method' => $order->payment_method_name,
                        'payment_channel' => $order->payment_channel_name,
                        'amount' => $order->total_amount,
                        'status' => 'pending',
                        'raw_response' => $tripayResult,
                    ]);
                }
            } catch (\Exception $e) {
                throw new \Exception('Gagal memproses pembayaran: ' . $e->getMessage());
            }

            return [
                'order' => $order,
                'tripay' => $tripayResult,
            ];
        });
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV/' . now()->format('Ymd') . '/';

        $lastOrder = Order::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->invoice_number, -5);
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        return $prefix . $newNumber;
    }
}
