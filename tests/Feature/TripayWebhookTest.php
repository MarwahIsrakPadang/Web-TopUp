<?php

namespace Tests\Feature;

use App\Enums\OrderStatusEnum;
use App\Jobs\ProcessOrderJob;
use App\Models\ApiConfig;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TripayWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const PRIVATE_KEY = 'test-private-key';

    private const MERCHANT_CODE = 'MERCHANT01';

    protected function setUp(): void
    {
        parent::setUp();

        ApiConfig::setConfig('tripay', 'api_key', 'test-api-key');
        ApiConfig::setConfig('tripay', 'private_key', self::PRIVATE_KEY);
        ApiConfig::setConfig('tripay', 'merchant_code', self::MERCHANT_CODE);
    }

    private function sign(string $merchantRef, int|string $amount): string
    {
        return hash_hmac('sha256', self::MERCHANT_CODE.$merchantRef.$amount, self::PRIVATE_KEY);
    }

    private function createPendingOrder(): Order
    {
        return Order::create([
            'invoice_number' => 'INV/20260825/00001',
            'player_id' => '12345678',
            'amount' => 20000,
            'fee' => 1000,
            'total_amount' => 21000,
            'status' => OrderStatusEnum::Pending,
            'payment_method_name' => 'QRIS',
            'payment_channel_name' => 'QRIS',
        ]);
    }

    private function postWebhook(array $payload): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/api/webhook/tripay', $payload);
    }

    public function test_rejects_invalid_signature(): void
    {
        $order = $this->createPendingOrder();

        $response = $this->postWebhook([
            'reference' => 'TRIPAY-REF-1',
            'merchant_ref' => $order->invoice_number,
            'status' => 'PAID',
            'amount' => 21000,
            'signature' => 'invalid-signature',
        ]);

        $response->assertStatus(401);
        $this->assertSame(OrderStatusEnum::Pending, $order->refresh()->status);
    }

    public function test_paid_webhook_updates_order_and_creates_transaction(): void
    {
        Queue::fake();
        $order = $this->createPendingOrder();

        $response = $this->postWebhook([
            'reference' => 'TRIPAY-REF-1',
            'merchant_ref' => $order->invoice_number,
            'status' => 'PAID',
            'amount' => 21000,
            'signature' => $this->sign($order->invoice_number, 21000),
        ]);

        $response->assertOk();
        $order->refresh();

        $this->assertSame(OrderStatusEnum::Paid, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame('TRIPAY-REF-1', $order->payment_reference);
        $this->assertSame(1, Transaction::where('order_id', $order->id)->count());
        Queue::assertPushed(ProcessOrderJob::class);
    }

    public function test_duplicate_paid_webhook_is_ignored(): void
    {
        $order = $this->createPendingOrder();

        $payload = [
            'reference' => 'TRIPAY-REF-1',
            'merchant_ref' => $order->invoice_number,
            'status' => 'PAID',
            'amount' => 21000,
            'signature' => $this->sign($order->invoice_number, 21000),
        ];

        $this->postWebhook($payload)->assertOk();
        $firstTransactionCount = Transaction::where('order_id', $order->id)->count();
        $paidAtAfterFirstCall = $order->refresh()->paid_at;

        // Callback duplikat (retry dari Tripay) harus idempotent
        $response = $this->postWebhook($payload);

        $response->assertOk();
        $this->assertSame($firstTransactionCount, Transaction::where('order_id', $order->id)->count());
        $this->assertEquals($paidAtAfterFirstCall, $order->refresh()->paid_at);
        $this->assertSame(1, $firstTransactionCount);
    }

    public function test_expired_webhook_does_not_override_paid_order(): void
    {
        Queue::fake();
        $order = $this->createPendingOrder();

        $this->postWebhook([
            'reference' => 'TRIPAY-REF-1',
            'merchant_ref' => $order->invoice_number,
            'status' => 'PAID',
            'amount' => 21000,
            'signature' => $this->sign($order->invoice_number, 21000),
        ])->assertOk();

        $this->postWebhook([
            'reference' => 'TRIPAY-REF-1',
            'merchant_ref' => $order->invoice_number,
            'status' => 'EXPIRED',
            'amount' => 21000,
            'signature' => $this->sign($order->invoice_number, 21000),
        ])->assertOk();

        $this->assertSame(OrderStatusEnum::Paid, $order->refresh()->status);
        Queue::assertPushed(ProcessOrderJob::class, 1);
    }

    public function test_unknown_status_is_rejected(): void
    {
        $order = $this->createPendingOrder();

        $response = $this->postWebhook([
            'reference' => 'TRIPAY-REF-1',
            'merchant_ref' => $order->invoice_number,
            'status' => 'WEIRD',
            'amount' => 21000,
            'signature' => $this->sign($order->invoice_number, 21000),
        ]);

        $response->assertStatus(400);
        $this->assertSame(OrderStatusEnum::Pending, $order->refresh()->status);
    }
}
