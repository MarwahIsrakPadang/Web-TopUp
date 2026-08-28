<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getSummary(array $filters): array
    {
        $query = Order::query();

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $summary = (clone $query)
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COALESCE(SUM(total_amount), 0) as total_revenue'),
                DB::raw('COALESCE(SUM(fee), 0) as total_fee'),
            )
            ->whereIn('status', [OrderStatusEnum::Paid, OrderStatusEnum::Processing, OrderStatusEnum::Success])
            ->first();

        $pendingCount = (clone $query)
            ->where('status', OrderStatusEnum::Pending)
            ->count();

        return [
            'total_orders' => (int) ($summary->total_orders ?? 0),
            'total_revenue' => (float) ($summary->total_revenue ?? 0),
            'total_fee' => (float) ($summary->total_fee ?? 0),
            'pending_count' => $pendingCount,
        ];
    }

    public function getDailySales(array $filters): Collection
    {
        $query = Order::whereIn('status', [OrderStatusEnum::Paid, OrderStatusEnum::Processing, OrderStatusEnum::Success]);

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COALESCE(SUM(total_amount), 0) as total_revenue'),
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getTopGames(array $filters): Collection
    {
        $query = Order::whereIn('status', [OrderStatusEnum::Paid, OrderStatusEnum::Processing, OrderStatusEnum::Success]);

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query
            ->select(
                'game_name',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COALESCE(SUM(total_amount), 0) as total_revenue'),
            )
            ->groupBy('game_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
    }
}
