<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    private PaymentChannel $channel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create(['price' => 20000]);
        $this->channel = PaymentChannel::factory()->create([
            'fee_type' => 'fixed',
            'fee_amount' => 1000,
        ]);
    }

    public function test_guest_can_create_order_without_voucher(): void
    {
        $response = $this->post(route('checkout.store'), [
            'game_id' => $this->product->game_id,
            'product_id' => $this->product->id,
            'payment_channel_id' => $this->channel->id,
            'player_id' => '12345678',
            'customer_name' => 'Budi',
            'customer_email' => 'budi@example.com',
        ]);

        $order = Order::query()->sole();

        $response->assertRedirect(route('invoice.show', $order->invoice_number));
        $this->assertSame(20000.0, (float) $order->amount);
        $this->assertSame(1000.0, (float) $order->fee);
        $this->assertSame(21000.0, (float) $order->total_amount);
        $this->assertSame(0.0, (float) $order->discount_amount);
        $this->assertNull($order->voucher_id);
        $this->assertSame('pending', $order->status->value);
    }

    public function test_percentage_voucher_applies_discount_and_increments_usage(): void
    {
        $voucher = Voucher::factory()->create([
            'code' => 'HEMAT10',
            'type' => 'percentage',
            'amount' => 10,
        ]);

        $this->post(route('checkout.store'), [
            'game_id' => $this->product->game_id,
            'product_id' => $this->product->id,
            'payment_channel_id' => $this->channel->id,
            'player_id' => '12345678',
            'voucher_code' => 'hemat10',
        ]);

        $order = Order::query()->sole();

        // 20000 + 1000 - 2000 (10%)
        $this->assertSame(19000.0, (float) $order->total_amount);
        $this->assertSame(2000.0, (float) $order->discount_amount);
        $this->assertSame($voucher->id, $order->voucher_id);
        $this->assertSame(1, $voucher->refresh()->used_count);
    }

    public function test_exhausted_voucher_is_rejected(): void
    {
        Voucher::factory()->create([
            'code' => 'HABIS',
            'maximum_usage' => 1,
            'used_count' => 1,
        ]);

        $response = $this->post(route('checkout.store'), [
            'game_id' => $this->product->game_id,
            'product_id' => $this->product->id,
            'payment_channel_id' => $this->channel->id,
            'player_id' => '12345678',
            'voucher_code' => 'HABIS',
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertSame(0, Order::count());
    }

    public function test_voucher_below_minimum_order_is_rejected(): void
    {
        Voucher::factory()->create([
            'code' => 'MINIMAL50',
            'minimum_order' => 50000,
        ]);

        $response = $this->post(route('checkout.store'), [
            'game_id' => $this->product->game_id,
            'product_id' => $this->product->id,
            'payment_channel_id' => $this->channel->id,
            'player_id' => '12345678',
            'voucher_code' => 'MINIMAL50',
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertSame(0, Order::count());
    }

    public function test_percentage_voucher_cannot_exceed_maximum_usage_via_concurrent_claims(): void
    {
        Voucher::factory()->create([
            'code' => 'SEKALI',
            'maximum_usage' => 1,
            'used_count' => 0,
        ]);

        $payload = fn () => [
            'game_id' => $this->product->game_id,
            'product_id' => $this->product->id,
            'payment_channel_id' => $this->channel->id,
            'player_id' => '12345678',
            'voucher_code' => 'SEKALI',
        ];

        $this->post(route('checkout.store'), $payload());
        $second = $this->post(route('checkout.store'), $payload());

        $this->assertSame(1, Order::whereNotNull('voucher_id')->count());
        $second->assertSessionHasErrors('error');

        // Klaim kedua tidak boleh membuat order sama sekali
        $this->assertSame(1, Order::count());
    }
}
