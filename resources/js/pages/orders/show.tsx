import { Head, Link, router } from '@inertiajs/react';
import { OrderStatusBadge } from '@/components/storefront/order-status-badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import StorefrontLayout from '@/layouts/storefront-layout';
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
        can_cancel: boolean;
        allowed_transitions: string[];
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
            image: string | null;
        }[];
    };
};

const steps = [
    { key: 'pending', label: 'Pending' },
    { key: 'paid', label: 'Paid' },
    { key: 'shipped', label: 'Shipped' },
    { key: 'completed', label: 'Completed' },
] as const;

function currentStepIndex(status: string): number {
    const map: Record<string, number> = {
        pending: 0,
        paid: 1,
        shipped: 2,
        completed: 3,
    };

    return map[status] ?? -1;
}

export default function OrderShow({ order }: Props) {
    const idx = currentStepIndex(order.status);
    const isCancelled = order.status === 'cancelled';
    const isFailed = order.status === 'failed';

    return (
        <StorefrontLayout>
            <Head title={`Pesanan ${order.order_number}`} />
            <div className="mx-auto max-w-5xl px-4 py-8">
                <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="font-mono text-xl font-bold">
                            {order.order_number}
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Dibuat{' '}
                            {order.created_at
                                ? new Date(order.created_at).toLocaleString(
                                      'id-ID',
                                  )
                                : '—'}
                        </p>
                    </div>
                    <OrderStatusBadge status={order.status} />
                </div>

                {/* Timeline */}
                {!isCancelled && !isFailed ? (
                    <div className="mb-6 flex gap-2">
                        {steps.map((s, i) => (
                            <div
                                key={s.key}
                                className={`flex flex-1 flex-col items-center rounded-lg border p-3 text-center text-sm ${
                                    i <= idx
                                        ? 'border-primary bg-primary/10'
                                        : 'bg-card'
                                }`}
                            >
                                <span className="font-medium">{s.label}</span>
                                {i === idx && (
                                    <span className="text-xs text-primary">
                                        ● Saat ini
                                    </span>
                                )}
                            </div>
                        ))}
                    </div>
                ) : (
                    <Card className="mb-6 border-destructive/50 bg-destructive/5">
                        <CardContent className="p-4 text-sm">
                            Pesanan ini <strong>{order.status}</strong>.
                            {order.cancelled_at && (
                                <span>
                                    {' '}
                                    Dibatalkan pada{' '}
                                    {new Date(
                                        order.cancelled_at,
                                    ).toLocaleString('id-ID')}
                                    .
                                </span>
                            )}
                        </CardContent>
                    </Card>
                )}

                <div className="grid gap-6 lg:grid-cols-[1fr_340px]">
                    <div className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">
                                    Item
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {order.items.map((it) => (
                                    <div key={it.id} className="flex gap-3">
                                        <div className="h-14 w-14 shrink-0 overflow-hidden rounded bg-muted">
                                            {it.image ? (
                                                <img
                                                    src={it.image}
                                                    alt=""
                                                    className="h-full w-full object-cover"
                                                />
                                            ) : null}
                                        </div>
                                        <div className="flex-1">
                                            <p className="text-sm font-medium">
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
                                        <div className="text-right text-sm">
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
                                        Alamat Pengiriman
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
                                {order.discount !== 0 && (
                                    <div className="flex justify-between">
                                        <span>Diskon</span>
                                        <span>
                                            -{formatIDR(order.discount)}
                                        </span>
                                    </div>
                                )}
                                <div className="flex justify-between border-t pt-2 font-semibold">
                                    <span>Total</span>
                                    <span>{formatIDR(order.total)}</span>
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    Metode bayar: {order.payment_method ?? '—'}
                                </p>
                                {order.payment_reference && (
                                    <p className="font-mono text-xs">
                                        Ref: {order.payment_reference}
                                    </p>
                                )}
                            </CardContent>
                        </Card>

                        {order.status === 'pending' &&
                            order.payment_status === 'unpaid' && (
                                <Button asChild className="w-full" size="lg">
                                    <Link
                                        href={`/orders/${order.order_number}/payment`}
                                    >
                                        Bayar Sekarang
                                    </Link>
                                </Button>
                            )}

                        {order.can_cancel && (
                            <Button
                                variant="destructive"
                                className="w-full"
                                onClick={() => {
                                    if (
                                        confirm(
                                            'Batalkan pesanan ini? Stok akan dikembalikan.',
                                        )
                                    ) {
                                        router.post(
                                            `/orders/${order.order_number}/cancel`,
                                        );
                                    }
                                }}
                            >
                                Batalkan Pesanan
                            </Button>
                        )}

                        <Button variant="outline" asChild className="w-full">
                            <Link href="/orders">Kembali ke Daftar</Link>
                        </Button>
                    </div>
                </div>
            </div>
        </StorefrontLayout>
    );
}
