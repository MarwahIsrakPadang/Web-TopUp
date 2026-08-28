<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Order;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();
        $yesterday = today()->subDay();
        $sevenDaysAgo = today()->subDays(6);
        $thirtyDaysAgo = today()->subDays(29);
        $ninetyDaysAgo = today()->subDays(89);

        // Today's stats
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $yesterdayOrders = Order::whereDate('created_at', $yesterday)->count();

        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', OrderStatusEnum::Success)
            ->sum('total_amount');

        $yesterdayRevenue = Order::whereDate('created_at', $yesterday)
            ->where('status', OrderStatusEnum::Success)
            ->sum('total_amount');

        $activeGames = Game::where('status', 'active')->count();
        $totalCustomers = User::where('role', UserRoleEnum::Customer)->count();

        // Pending transactions
        $pendingTransactionsCount = Order::where('status', OrderStatusEnum::Pending)->count();

        // Calculate changes
        $ordersChange = $yesterdayOrders > 0
            ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100)
            : ($todayOrders > 0 ? 100 : 0);

        $revenueChange = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100)
            : ($todayRevenue > 0 ? 100 : 0);

        return Inertia::render('Dashboard', [
            'stats' => [
                'todayOrders' => $todayOrders,
                'todayRevenue' => (int) $todayRevenue,
                'activeGames' => $activeGames,
                'totalCustomers' => $totalCustomers,
                'ordersChange' => ($ordersChange >= 0 ? '+' : '') . $ordersChange . '%',
                'ordersChangeType' => $ordersChange >= 0 ? 'up' : 'down',
                'revenueChange' => ($revenueChange >= 0 ? '+' : '') . $revenueChange . '%',
                'revenueChangeType' => $revenueChange >= 0 ? 'up' : 'down',
            ],
            'revenueChartData' => $this->revenueChartData([7 => $sevenDaysAgo, 30 => $thirtyDaysAgo, 90 => $ninetyDaysAgo]),
            'paymentMethodDistribution' => $this->paymentMethodDistribution(),
            'recentTransactions' => $this->recentTransactions(),
            'pendingTransactionsCount' => $pendingTransactionsCount,
        ]);
    }

    private function revenueChartData(array $periods): array
    {
        $today = today();
        $chartData = [];

        foreach ($periods as $days => $startDate) {
            $data = Order::where('status', OrderStatusEnum::Success)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $today)
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total_revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $periodData = [];
            for ($i = 0; $i < $days; $i++) {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                $periodData[] = [
                    'date' => $date,
                    'total_revenue' => $data[$date]->total_revenue ?? 0,
                ];
            }

            $chartData[$days] = $periodData;
        }

        return $chartData;
    }

    private function paymentMethodDistribution()
    {
        $distribution = Order::query()
            ->where('orders.status', OrderStatusEnum::Success)
            ->whereNotNull('orders.payment_method_id')
            ->join('payment_methods', 'payment_methods.id', '=', 'orders.payment_method_id')
            ->groupBy('payment_methods.name')
            ->selectRaw('payment_methods.name as name, COUNT(*) as count')
            ->orderByDesc('count')
            ->get();

        $totalPaidOrders = $distribution->sum('count');

        return $distribution->map(function ($method) use ($totalPaidOrders) {
            return [
                'name' => $method->name,
                'count' => (int) $method->count,
                'percentage' => $totalPaidOrders > 0
                    ? round(($method->count / $totalPaidOrders) * 100)
                    : 0,
            ];
        });
    }

    private function recentTransactions()
    {
        return Order::with(['game', 'product', 'paymentMethod'])
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(function ($order) {
                return [
                    'id' => $order->id,
                    'invoice' => $order->invoice_number,
                    'game' => $order->game?->name ?? '-',
                    'product' => $order->product?->name ?? '-',
                    'payment_method' => $order->paymentMethod?->name ?? '-',
                    'amount' => (int) $order->total_amount,
                    'status' => $order->status->value ?? $order->status,
                    'created_at' => $order->created_at?->toISOString(),
                ];
            });
    }
}
