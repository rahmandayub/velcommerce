<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    /**
     * @return list<string>
     */
    private function paidStatuses(): array
    {
        return [
            OrderStatus::Paid->value,
            OrderStatus::Shipped->value,
            OrderStatus::Completed->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function kpis(): array
    {
        $paid = $this->paidStatuses();
        $revenue = (float) Order::whereIn('status', $paid)->sum('total');
        $paidCount = Order::whereIn('status', $paid)->count();
        $pendingCount = Order::where('status', OrderStatus::Pending->value)->count();
        $ordersCount = Order::count();
        $avgOrderValue = $paidCount > 0 ? $revenue / $paidCount : 0;

        return [
            'revenue' => $revenue,
            'orders_count' => $ordersCount,
            'pending_count' => $pendingCount,
            'paid_count' => $paidCount,
            'avg_order_value' => round($avgOrderValue, 2),
            'low_stock_count' => $this->lowStock((int) config('shop.low_stock_threshold', 5))->count(),
        ];
    }

    /**
     * @return array<int, array{label: string, revenue: float, orders: int}>
     */
    public function salesChart(string $range = '30d'): array
    {
        $days = match ($range) {
            '7d' => 7,
            '90d' => 90,
            '12m' => 12,
            default => 30,
        };

        $monthly = $range === '12m';
        $start = $monthly
            ? now()->subMonths(11)->startOfMonth()
            : now()->subDays($days - 1)->startOfDay();

        $orders = Order::query()
            ->whereIn('status', $this->paidStatuses())
            ->where('created_at', '>=', $start)
            ->get(['created_at', 'total']);

        $buckets = [];

        if ($monthly) {
            for ($i = 0; $i < 12; $i++) {
                $key = $start->copy()->addMonths($i)->format('Y-m');
                $buckets[$key] = [
                    'label' => $start->copy()->addMonths($i)->format('M Y'),
                    'revenue' => 0.0,
                    'orders' => 0,
                ];
            }
        } else {
            for ($i = 0; $i < $days; $i++) {
                $key = $start->copy()->addDays($i)->format('Y-m-d');
                $buckets[$key] = [
                    'label' => $start->copy()->addDays($i)->format('d M'),
                    'revenue' => 0.0,
                    'orders' => 0,
                ];
            }
        }

        foreach ($orders as $order) {
            $key = $monthly
                ? $order->created_at->format('Y-m')
                : $order->created_at->format('Y-m-d');

            if (isset($buckets[$key])) {
                $buckets[$key]['revenue'] += (float) $order->total;
                $buckets[$key]['orders'] += 1;
            }
        }

        return array_values($buckets);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function topProducts(int $limit = 5): array
    {
        $rows = OrderItem::query()
            ->selectRaw('product_id, product_name, SUM(quantity) as qty, SUM(order_items.subtotal) as revenue')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', $this->paidStatuses())
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('qty')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $products = Product::query()
            ->with('images')
            ->whereIn('id', $rows->pluck('product_id')->all())
            ->get()
            ->keyBy('id');

        return $rows->map(function (OrderItem $row) use ($products): array {
            /** @var Product|null $product */
            $product = $products->get($row->product_id);

            return [
                'product_id' => $row->product_id,
                'name' => $row->product_name,
                'image' => $product?->images->first()?->url,
                'qty' => (int) $row->qty,
                'revenue' => (float) $row->revenue,
                'edit_url' => $product
                    ? route('admin.products.edit', ['product' => $product->id])
                    : null,
            ];
        })->all();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function lowStock(int $threshold): Collection
    {
        return Product::query()
            ->where('stock', '<=', $threshold)
            ->orWhereHas('variants', fn ($q) => $q->where('stock', '<=', $threshold))
            ->with([
                'variants' => fn ($q) => $q->where('stock', '<=', $threshold),
                'images',
            ])
            ->orderBy('stock')
            ->limit(20)
            ->get()
            ->map(function (Product $product): array {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'stock' => $product->stock,
                    'image' => $product->images->first()?->url,
                    'edit_url' => route('admin.products.edit', ['product' => $product->id]),
                    'variants' => $product->variants->map(fn ($v) => [
                        'id' => $v->id,
                        'name' => $v->name,
                        'sku' => $v->sku,
                        'stock' => $v->stock,
                    ])->all(),
                ];
            });
    }
}
