import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { OrderStatusBadge } from '@/components/storefront/order-status-badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { formatIDR } from '@/lib/format';

type Props = {
    order: {
        id: number;
        order_number: string;
        status: string;
        payment_status: string;
        payment_method: string | null;
        payment_reference: string | null;
        subtotal: number;
        shipping_cost: number;
        discount: number;
        tax: number;
        total: number;
        notes: string | null;
        created_at: string | null;
        paid_at: string | null;
        shipped_at: string | null;
        completed_at: string | null;
        cancelled_at: string | null;
        allowed_transitions: string[];
        user: { name: string; email: string } | null;
        address: {
            label: string;
            recipient_name: string;
            phone: string;
            full_address: string;
        } | null;
        items: {
            id: number;
            product_name: string;
            variant_name: string | null;
            sku: string;
            price: number;
            quantity: number;
            subtotal: number;
            attributes: Record<string, string> | null;
        }[];
    };
};

export default function AdminOrderShow({ order }: Props) {
    const [nextStatus, setNextStatus] = useState(
        order.allowed_transitions[0] ?? '',
    );

    function updateStatus() {
        if (!nextStatus) {
            return;
        }

        router.post(`/admin/orders/${order.id}/status`, { status: nextStatus });
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin' },
                { title: 'Orders', href: '/admin/orders' },
                {
                    title: order.order_number,
                    href: `/admin/orders/${order.id}`,
                },
            ]}
        >
            <Head title={`Admin — ${order.order_number}`} />
            <div className="p-4">
                <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="font-mono text-lg font-bold">
                            {order.order_number}
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Customer: {order.user?.name ?? '—'} (
                            {order.user?.email ?? '—'})
                        </p>
                    </div>
                    <OrderStatusBadge status={order.status} />
                </div>

                <div className="grid gap-4 lg:grid-cols-[1fr_360px]">
                    <div className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">
                                    Items
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-2">
                                {order.items.map((it) => (
                                    <div
                                        key={it.id}
                                        className="flex justify-between text-sm"
                                    >
                                        <div>
                                            <p className="font-medium">
                                                {it.product_name}
                                            </p>
                                            {it.variant_name && (
                                                <p className="text-xs text-muted-foreground">
                                                    {it.variant_name}
                                                </p>
                                            )}
                                            <p className="text-xs text-muted-foreground">
                                                {it.sku}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <p>
                                                {formatIDR(it.price)} ×{' '}
                                                {it.quantity}
                                            </p>
                                            <p className="font-semibold">
                                                {formatIDR(it.subtotal)}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </CardContent>
                        </Card>

                        {order.address && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-base">
                                        Alamat
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="text-sm">
                                    <p className="font-medium">
                                        {order.address.label} —{' '}
                                        {order.address.recipient_name}
                                    </p>
                                    <p>{order.address.phone}</p>
                                    <p className="text-muted-foreground">
                                        {order.address.full_address}
                                    </p>
                                </CardContent>
                            </Card>
                        )}
                    </div>

                    <div className="space-y-4">
                        <Card>
                            <CardContent className="space-y-2 p-4 text-sm">
                                <div className="flex justify-between">
                                    <span>Subtotal</span>
                                    <span>{formatIDR(order.subtotal)}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Ongkir</span>
                                    <span>
                                        {formatIDR(order.shipping_cost)}
                                    </span>
                                </div>
                                <div className="flex justify-between border-t pt-2 font-semibold">
                                    <span>Total</span>
                                    <span>{formatIDR(order.total)}</span>
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    Payment: {order.payment_status}
                                </p>
                                {order.payment_reference && (
                                    <p className="font-mono text-xs">
                                        Ref: {order.payment_reference}
                                    </p>
                                )}
                                <div className="space-y-1 pt-2 text-xs text-muted-foreground">
                                    {order.paid_at && (
                                        <p>
                                            Paid:{' '}
                                            {new Date(
                                                order.paid_at,
                                            ).toLocaleString('id-ID')}
                                        </p>
                                    )}
                                    {order.shipped_at && (
                                        <p>
                                            Shipped:{' '}
                                            {new Date(
                                                order.shipped_at,
                                            ).toLocaleString('id-ID')}
                                        </p>
                                    )}
                                    {order.completed_at && (
                                        <p>
                                            Completed:{' '}
                                            {new Date(
                                                order.completed_at,
                                            ).toLocaleString('id-ID')}
                                        </p>
                                    )}
                                    {order.cancelled_at && (
                                        <p>
                                            Cancelled:{' '}
                                            {new Date(
                                                order.cancelled_at,
                                            ).toLocaleString('id-ID')}
                                        </p>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">
                                    Update Status
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {order.allowed_transitions.length === 0 ? (
                                    <p className="text-sm text-muted-foreground">
                                        Tidak ada transisi tersedia.
                                    </p>
                                ) : (
                                    <>
                                        <Select
                                            value={nextStatus}
                                            onValueChange={setNextStatus}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Pilih status" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {order.allowed_transitions.map(
                                                    (s) => (
                                                        <SelectItem
                                                            key={s}
                                                            value={s}
                                                        >
                                                            {s}
                                                        </SelectItem>
                                                    ),
                                                )}
                                            </SelectContent>
                                        </Select>
                                        <Button
                                            onClick={updateStatus}
                                            className="w-full"
                                        >
                                            Update ke {nextStatus || '—'}
                                        </Button>
                                    </>
                                )}
                                <Button
                                    variant="outline"
                                    asChild
                                    className="w-full"
                                >
                                    <Link href="/admin/orders">Kembali</Link>
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
