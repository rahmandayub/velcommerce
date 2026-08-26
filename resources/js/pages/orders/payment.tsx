import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
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
        total: number;
    };
};

export default function OrderPayment({ order }: Props) {
    const [processing, setProcessing] = useState(false);

    function callback(outcome: 'paid' | 'failed') {
        setProcessing(true);
        router.post(
            `/orders/${order.order_number}/mock-callback`,
            { outcome },
            {
                onFinish: () => setProcessing(false),
            },
        );
    }

    return (
        <StorefrontLayout>
            <Head title={`Bayar ${order.order_number}`} />
            <div className="mx-auto max-w-lg px-4 py-12">
                <Card>
                    <CardHeader className="text-center">
                        <CardTitle>Mock Payment Gateway</CardTitle>
                        <p className="text-sm text-muted-foreground">
                            Ini adalah halaman pembayaran mock (hanya di
                            local/testing/staging). Pilih outcome untuk
                            mensimulasikan callback gateway.
                        </p>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="rounded-lg border bg-muted p-4 text-center">
                            <p className="font-mono text-sm">
                                {order.order_number}
                            </p>
                            <p className="mt-1 text-2xl font-bold">
                                {formatIDR(order.total)}
                            </p>
                            <p className="text-xs text-muted-foreground">
                                Status: {order.status} · Payment:{' '}
                                {order.payment_status}
                            </p>
                        </div>

                        <div className="grid gap-3">
                            <Button
                                size="lg"
                                disabled={processing}
                                onClick={() => callback('paid')}
                            >
                                Pay now (Sukses)
                            </Button>
                            <Button
                                size="lg"
                                variant="destructive"
                                disabled={processing}
                                onClick={() => callback('failed')}
                            >
                                Fail (Gagal)
                            </Button>
                        </div>

                        <p className="text-center text-xs text-muted-foreground">
                            Klik Pay untuk mengubah status menjadi{' '}
                            <strong>paid</strong> dan melanjutkan ke tracking
                            pesanan. Klik Fail untuk mensimulasikan pembayaran
                            gagal.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </StorefrontLayout>
    );
}
