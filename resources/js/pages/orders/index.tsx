import { Head, Link } from '@inertiajs/react';
import { OrderStatusBadge } from '@/components/storefront/order-status-badge';
import { Pagination } from '@/components/storefront/pagination';
import { Card, CardContent } from '@/components/ui/card';
import StorefrontLayout from '@/layouts/storefront-layout';
import { formatIDR } from '@/lib/format';

type Order = {
    id: number;
    order_number: string;
    status: string;
    payment_status: string;
    total: number;
    subtotal: number;
    shipping_cost: number;
    created_at: string | null;
    items_count: number;
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    orders: Paginated<Order>;
};

export default function OrdersIndex({ orders }: Props) {
    return (
        <StorefrontLayout>
            <Head title="Pesanan Saya" />
            <div className="mx-auto max-w-5xl px-4 py-8">
                <h1 className="mb-6 text-2xl font-bold">Riwayat Pesanan</h1>

                {orders.data.length === 0 ? (
                    <Card className="p-12 text-center">
                        <p className="text-muted-foreground">Belum ada pesanan.</p>
                        <Link href="/products" className="mt-4 inline-block text-sm text-primary hover:underline">
                            Mulai belanja
                        </Link>
                    </Card>
                ) : (
                    <div className="space-y-3">
                        {orders.data.map((order) => (
                            <Card key={order.id}>
                                <CardContent className="flex flex-wrap items-center justify-between gap-3 p-4">
                                    <div>
                                        <Link
                                            href={`/orders/${order.order_number}`}
                                            className="font-mono text-sm font-semibold hover:underline"
                                        >
                                            {order.order_number}
                                        </Link>
                                        <p className="text-xs text-muted-foreground">
                                            {order.created_at
                                                ? new Date(order.created_at).toLocaleString('id-ID')
                                                : '—'}{' '}
                                            · {order.items_count} item
                                        </p>
                                        <p className="mt-1 text-sm font-medium">
                                            {formatIDR(order.total)}
                                        </p>
                                    </div>
                                    <div className="flex flex-col items-end gap-2">
                                        <OrderStatusBadge status={order.status} />
                                        <Link
                                            href={`/orders/${order.order_number}`}
                                            className="text-xs text-primary hover:underline"
                                        >
                                            Lihat detail
                                        </Link>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}

                        <Pagination links={orders.links} />
                    </div>
                )}
            </div>
        </StorefrontLayout>
    );
}
