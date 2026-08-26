import { Head, Link } from '@inertiajs/react';
import { OrderStatusBadge } from '@/components/storefront/order-status-badge';
import { Pagination } from '@/components/storefront/pagination';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { formatIDR } from '@/lib/format';

type Order = {
    id: number;
    order_number: string;
    user: { name: string; email: string } | null;
    status: string;
    payment_status: string;
    total: number;
    items_count: number;
    created_at: string | null;
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    orders: Paginated<Order>;
};

export default function AdminOrdersIndex({ orders }: Props) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin' },
                { title: 'Orders', href: '/admin/orders' },
            ]}
        >
            <Head title="Admin — Orders" />
            <div className="p-4">
                <h1 className="mb-4 text-xl font-semibold">Orders</h1>

                <Card>
                    <CardContent className="p-0">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="border-b bg-muted/50">
                                    <tr>
                                        <th className="p-3 text-left">Order</th>
                                        <th className="p-3 text-left">
                                            Customer
                                        </th>
                                        <th className="p-3 text-center">
                                            Status
                                        </th>
                                        <th className="p-3 text-right">
                                            Total
                                        </th>
                                        <th className="p-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {orders.data.map((o) => (
                                        <tr
                                            key={o.id}
                                            className="border-b last:border-0"
                                        >
                                            <td className="p-3">
                                                <Link
                                                    href={`/admin/orders/${o.id}`}
                                                    className="font-mono text-xs font-medium hover:underline"
                                                >
                                                    {o.order_number}
                                                </Link>
                                                <p className="text-xs text-muted-foreground">
                                                    {o.created_at
                                                        ? new Date(
                                                              o.created_at,
                                                          ).toLocaleString(
                                                              'id-ID',
                                                          )
                                                        : '—'}{' '}
                                                    · {o.items_count} item
                                                </p>
                                            </td>
                                            <td className="p-3">
                                                <p className="text-sm">
                                                    {o.user?.name ?? '—'}
                                                </p>
                                                <p className="text-xs text-muted-foreground">
                                                    {o.user?.email ?? ''}
                                                </p>
                                            </td>
                                            <td className="p-3 text-center">
                                                <OrderStatusBadge
                                                    status={o.status}
                                                />
                                            </td>
                                            <td className="p-3 text-right">
                                                {formatIDR(o.total)}
                                            </td>
                                            <td className="p-3 text-right">
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    asChild
                                                >
                                                    <Link
                                                        href={`/admin/orders/${o.id}`}
                                                    >
                                                        Detail
                                                    </Link>
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-4">
                    <Pagination links={orders.links} />
                </div>
            </div>
        </AppLayout>
    );
}
