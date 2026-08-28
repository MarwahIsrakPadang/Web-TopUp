<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function getByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('user_id', $userId)
            ->with(['game', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getBySession(int $perPage = 10): LengthAwarePaginator
    {
        $orderIds = session('order_ids', []);

        if (empty($orderIds)) {
            return Order::whereRaw('1 = 0')->paginate($perPage);
        }

        return Order::whereIn('id', $orderIds)
            ->with(['game', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByPhone(string $phone, int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('customer_phone', $phone)
            ->with(['game', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
