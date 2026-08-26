import { Head, Link, router } from '@inertiajs/react';
import { CouponInput } from '@/components/storefront/coupon-input';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import StorefrontLayout from '@/layouts/storefront-layout';
import { formatIDR } from '@/lib/format';

type AppliedCoupon = {
    code: string;
    type: 'percent' | 'fixed';
    value: number;
    discount: number;
};

type Props = {
    address: {
        id: number;
        label: string;
        recipient_name: string;
        phone: string;
        full_address: string;
    };
    cart: {
        items: { id: number; product_name: string | null; variant_name: string | null; price: number; quantity: number; subtotal: number; image: string | null }[];
        subtotal: number;
        shipping_cost: number;
        discount: number;
        total: number;
    };
    coupon: AppliedCoupon | null;
};

export default function CheckoutConfirm({ address, cart, coupon }: Props) {
    function placeOrder() {
        router.post('/checkout', { address_id: address.id });
    }

    return (
        <StorefrontLayout>
            <Head title="Checkout — Konfirmasi" />
            <div className="mx-auto max-w-5xl px-4 py-8">
                <h1 className="mb-2 text-2xl font-bold">Konfirmasi Pesanan</h1>
                <p className="mb-6 text-sm text-muted-foreground">Langkah 2 dari 2 — periksa kembali pesanan Anda</p>

                <div className="grid gap-6 lg:grid-cols-[1fr_360px]">
                    <div className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">Alamat Pengiriman</CardTitle>
                            </CardHeader>
                            <CardContent className="text-sm">
                                <p className="font-medium">{address.label} — {address.recipient_name}</p>
                                <p>{address.phone}</p>
                                <p className="text-muted-foreground">{address.full_address}</p>
                                <Button variant="link" size="sm" asChild className="px-0">
                                    <Link href="/checkout/address">Ganti alamat</Link>
                                </Button>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">Item Pesanan</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {cart.items.map((it) => (
                                    <div key={it.id} className="flex gap-3">
                                        <div className="h-14 w-14 shrink-0 overflow-hidden rounded bg-muted">
                                            {it.image ? <img src={it.image} alt="" className="h-full w-full object-cover" /> : null}
                                        </div>
                                        <div className="flex-1">
                                            <p className="text-sm font-medium">{it.product_name}</p>
                                            {it.variant_name && <p className="text-xs text-muted-foreground">{it.variant_name}</p>}
                                            <p className="text-xs">{formatIDR(it.price)} × {it.quantity}</p>
                                        </div>
                                        <p className="text-sm font-semibold">{formatIDR(it.subtotal)}</p>
                                    </div>
                                ))}
                            </CardContent>
                        </Card>
                    </div>

                    <Card className="h-fit">
                        <CardHeader>
                            <CardTitle className="text-base">Ringkasan Biaya</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <div className="flex justify-between text-sm">
                                <span>Subtotal</span>
                                <span>{formatIDR(cart.subtotal)}</span>
                            </div>
                            <div className="flex justify-between text-sm">
                                <span>Ongkir</span>
                                <span>{formatIDR(cart.shipping_cost)}</span>
                            </div>
                            <CouponInput coupon={coupon} />
                            {cart.discount > 0 && (
                                <div className="flex justify-between text-sm text-primary">
                                    <span>Diskon {coupon ? `(${coupon.code})` : ''}</span>
                                    <span>-{formatIDR(cart.discount)}</span>
                                </div>
                            )}
                            <div className="flex justify-between border-t pt-3 font-semibold">
                                <span>Total</span>
                                <span>{formatIDR(cart.total)}</span>
                            </div>
                            <Button size="lg" className="w-full" onClick={placeOrder}>
                                Place Order
                            </Button>
                            <p className="text-center text-xs text-muted-foreground">
                                Dengan menekan Place Order Anda menyetujui syarat & ketentuan.
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </StorefrontLayout>
    );
}
