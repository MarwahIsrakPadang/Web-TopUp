<?php

namespace App\Services;

use App\Models\ApiConfig;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayService
{
    private readonly string $apiKey;
    private readonly string $privateKey;
    private readonly string $merchantCode;
    private readonly bool $isProduction;

    public function __construct()
    {
        $this->apiKey = ApiConfig::getConfig('tripay', 'api_key', '');
        $this->privateKey = ApiConfig::getConfig('tripay', 'private_key', '');
        $this->merchantCode = ApiConfig::getConfig('tripay', 'merchant_code', '');
        $this->isProduction = ApiConfig::getConfig('tripay', 'is_production', '0') === '1';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey)
            && !empty($this->privateKey)
            && !empty($this->merchantCode);
    }

    public function getApiBaseUrl(): string
    {
        return $this->isProduction
            ? 'https://tripay.co.id/api/'
            : 'https://tripay.co.id/api-sandbox/';
    }

    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ];
    }

    public function getPaymentChannels(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->getApiBaseUrl() . 'merchant/payment-channel');

        if ($response->failed()) {
            Log::error('Tripay Payment Channel Error: ' . $response->body());
            throw new \Exception('Gagal mengambil channel pembayaran dari Tripay.');
        }

        return $response->json()['data'] ?? [];
    }

    public function createTransaction(Order $order, array $customerData): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Konfigurasi Tripay belum lengkap.');
        }

        $channel = $order->paymentChannel;
        $expiredTime = now()->addHours(24)->timestamp;

        $payload = [
            'method' => $channel->code,
            'merchant_ref' => $order->invoice_number,
            'amount' => (int) $order->total_amount,
            'customer_name' => $customerData['name'] ?? 'Guest',
            'customer_email' => $customerData['email'] ?? '',
            'customer_phone' => $customerData['phone'] ?? '',
            'order_items' => [
                [
                    'sku' => (string) $order->product_id,
                    'name' => $order->product_name,
                    'price' => (int) $order->total_amount,
                    'quantity' => 1,
                ],
            ],
            'return_url' => route('invoice.show', $order->invoice_number),
            'expired_time' => $expiredTime,
            'signature' => $this->generateSignature($order->invoice_number, (int) $order->total_amount),
        ];

        $response = Http::withHeaders($this->headers())
            ->post($this->getApiBaseUrl() . 'transaction/create', $payload);

        if ($response->failed()) {
            Log::error('Tripay Create Transaction Error: ' . $response->body(), [
                'order_invoice' => $order->invoice_number,
                'payload' => $payload
            ]);
            throw new \Exception(
                'Tripay error: ' . ($response->json()['message'] ?? 'Gagal membuat transaksi.')
            );
        }

        return $response->json()['data'] ?? [];
    }

    public function getDetailTransaction(string $reference): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->getApiBaseUrl() . 'transaction/detail', [
                'reference' => $reference,
            ]);

        if ($response->failed()) {
            Log::error('Tripay Get Detail Transaction Error: ' . $response->body(), [
                'reference' => $reference
            ]);
            throw new \Exception('Gagal mengambil detail transaksi dari Tripay.');
        }

        return $response->json()['data'] ?? [];
    }

    public function generateSignature(string $merchantRef, int $amount): string
    {
        return hash_hmac('sha256', $this->merchantCode . $merchantRef . $amount, $this->privateKey);
    }

    public function verifyCallbackSignature(array $payload): bool
    {
        $signature = $payload['signature'] ?? '';
        $merchantRef = $payload['merchant_ref'] ?? '';
        $amount = $payload['amount'] ?? 0;

        $expected = hash_hmac('sha256', $this->merchantCode . $merchantRef . $amount, $this->privateKey);

        return hash_equals($expected, $signature);
    }
}
