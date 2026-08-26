import { Head, router } from '@inertiajs/react';
import { AlertTriangle, DollarSign, Package, ShoppingCart, Star, TrendingUp } from 'lucide-react';
import { SalesChart } from '@/components/admin/sales-chart';
import { StatCard } from '@/components/admin/stat-card';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { formatIDR } from '@/lib/format';
import { index as adminCoupons } from '@/routes/admin/coupons';
import { index as adminProducts } from '@/routes/admin/products';
import { index as adminReviews } from '@/routes/admin/reviews';

type ChartPoint = { label: string; revenue: number; orders: number };
type TopProduct = {
    product_id: number;
    name: string;
    image: string | null;
    qty: number;
    revenue: number;
    edit_url: string | null;
};
type LowStockItem = {
    id: number;
    name: string;
    slug: string;
    stock: number;
    image: string | null;
    edit_url: string;
    variants: { id: number; name: string | null; sku: string; stock: number }[];
};

type Kpis = {
    revenue: number;
    orders_count: number;
    pending_count: number;
    paid_count: number;
    avg_order_value: number;
    low_stock_count: number;
};

type Props = {
    range: string;
    kpis: Kpis;
    chartData: ChartPoint[];
    topProducts: TopProduct[];
    lowStock: LowStockItem[];
};

const ranges = [
    { value: '7d', label: '7 Hari' },
    { value: '30d', label: '30 Hari' },
    { value: '90d', label: '90 Hari' },
    { value: '12m', label: '12 Bulan' },
];

export default function AdminDashboard({
    range,
    kpis,
    chartData,
    topProducts,
    lowStock,
}: Props) {
    function changeRange(value: string) {
        router.get(
            '/admin',
            { range: value },
            { preserveState: true, preserveScroll: true, only: ['chartData', 'range', 'kpis'] },
        );
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin' },
                { title: 'Dashboard', href: '/admin' },
            ]}
        >
            <Head title="Admin — Dashboard" />
            <div className="space-y-6 p-4">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-bold">Dashboard</h1>
                        <p className="text-sm text-muted-foreground">
                            Ringkasan performa toko.
                        </p>
                    </div>
                    <div className="flex gap-1">
                        {ranges.map((r) => (
                            <Button
                                key={r.value}
                                size="sm"
                                variant={r.value === range ? 'default' : 'outline'}
                                onClick={() => changeRange(r.value)}
                            >
                                {r.label}
                            </Button>
                        ))}
                    </div>
                </div>

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    <StatCard
                        title="Pendapatan"
                        value={formatIDR(kpis.revenue)}
                        icon={<DollarSign className="h-5 w-5" />}
                    />
                    <StatCard
                        title="Total Pesanan"
                        value={String(kpis.orders_count)}
                        icon={<ShoppingCart className="h-5 w-5" />}
                    />
                    <StatCard
                        title="Menunggu"
                        value={String(kpis.pending_count)}
                        icon={<Package className="h-5 w-5" />}
                    />
                    <StatCard
                        title="Rata-rata"
                        value={formatIDR(kpis.avg_order_value)}
                        icon={<TrendingUp className="h-5 w-5" />}
                    />
                    <StatCard
                        title="Stok Rendah"
                        value={String(kpis.low_stock_count)}
                        icon={<AlertTriangle className="h-5 w-5" />}
                    />
                    <StatCard
                        title="Terbayar"
                        value={String(kpis.paid_count)}
                        icon={<Star className="h-5 w-5" />}
                    />
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-base">Grafik Penjualan</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <SalesChart data={chartData} />
                    </CardContent>
                </Card>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base">Produk Terlaris</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {topProducts.length === 0 ? (
                                <p className="text-sm text-muted-foreground">
                                    Belum ada penjualan.
                                </p>
                            ) : (
                                topProducts.map((p, i) => (
                                    <div key={p.product_id} className="flex items-center gap-3">
                                        <span className="w-5 text-sm font-medium text-muted-foreground">
                                            {i + 1}
                                        </span>
                                        {p.image ? (
                                            <img
                                                src={p.image}
                                                alt=""
                                                className="h-10 w-10 rounded object-cover"
                                            />
                                        ) : (
                                            <div className="h-10 w-10 rounded bg-muted" />
                                        )}
                                        <div className="flex-1">
                                            <p className="text-sm font-medium">{p.name}</p>
                                            <p className="text-xs text-muted-foreground">
                                                {p.qty} terjual
                                            </p>
                                        </div>
                                        <span className="text-sm font-semibold">
                                            {formatIDR(p.revenue)}
                                        </span>
                                    </div>
                                ))
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base">Stok Rendah</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {lowStock.length === 0 ? (
                                <p className="text-sm text-muted-foreground">
                                    Semua stok aman.
                                </p>
                            ) : (
                                lowStock.map((item) => (
                                    <div key={item.id} className="rounded-md border p-3">
                                        <div className="flex items-center gap-3">
                                            {item.image ? (
                                                <img
                                                    src={item.image}
                                                    alt=""
                                                    className="h-10 w-10 rounded object-cover"
                                                />
                                            ) : (
                                                <div className="h-10 w-10 rounded bg-muted" />
                                            )}
                                            <div className="flex-1">
                                                <p className="text-sm font-medium">
                                                    {item.name}
                                                </p>
                                                <p className="text-xs text-muted-foreground">
                                                    Stok produk: {item.stock}
                                                </p>
                                            </div>
                                            <Button size="sm" variant="outline" asChild>
                                                <a href={item.edit_url}>Restok</a>
                                            </Button>
                                        </div>
                                        {item.variants.length > 0 && (
                                            <div className="mt-2 space-y-1">
                                                {item.variants.map((v) => (
                                                    <div
                                                        key={v.id}
                                                        className="flex justify-between text-xs text-muted-foreground"
                                                    >
                                                        <span>
                                                            {v.name ?? v.sku}
                                                        </span>
                                                        <span
                                                            className={
                                                                v.stock <= 0
                                                                    ? 'text-destructive'
                                                                    : ''
                                                            }
                                                        >
                                                            stok {v.stock}
                                                        </span>
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                    </div>
                                ))
                            )}
                        </CardContent>
                    </Card>
                </div>

                <div className="flex flex-wrap gap-2">
                    <Button variant="outline" asChild>
                        <a href={adminProducts().url}>Kelola Produk</a>
                    </Button>
                    <Button variant="outline" asChild>
                        <a href={adminCoupons().url}>Kelola Kupon</a>
                    </Button>
                    <Button variant="outline" asChild>
                        <a href={adminReviews().url}>Moderasi Ulasan</a>
                    </Button>
                </div>
            </div>
        </AppLayout>
    );
}
