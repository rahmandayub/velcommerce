import { Head, Link, router } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import StorefrontLayout from '@/layouts/storefront-layout';
import { formatIDR } from '@/lib/format';

type CartItem = {
    id: number;
    product_id: number;
    product_name: string | null;
    product_slug: string | null;
    variant_id: number | null;
    variant_name: string | null;
    variant_attributes: Record<string, string> | null;
    price: number;
    quantity: number;
    subtotal: number;
    stock: number;
    image: string | null;
    is_active: boolean;
};

type Props = {
    items: CartItem[];
    subtotal: number;
    count: number;
};

export default function CartIndex({ items, subtotal }: Props) {
    const shipping = 15000;
    const total = subtotal + shipping;

    function updateQty(item: CartItem, qty: number) {
        router.patch(
            `/cart/items/${item.id}`,
            { quantity: qty },
            { preserveScroll: true },
        );
    }

    function remove(item: CartItem) {
        router.delete(`/cart/items/${item.id}`, { preserveScroll: true });
    }

    return (
        <StorefrontLayout>
            <Head title="Keranjang" />
            <div className="mx-auto max-w-5xl px-4 py-8">
                <h1 className="mb-6 text-2xl font-bold">Keranjang Belanja</h1>

                {items.length === 0 ? (
                    <Card className="p-12 text-center">
                        <p className="text-muted-foreground">
                            Keranjang masih kosong.
                        </p>
                        <Button asChild className="mt-4">
                            <Link href="/products">Jelajahi Katalog</Link>
                        </Button>
                    </Card>
                ) : (
                    <div className="grid gap-6 lg:grid-cols-[1fr_340px]">
                        <div className="space-y-3">
                            {items.map((item) => (
                                <Card key={item.id}>
                                    <CardContent className="flex gap-4 p-4">
                                        <div className="h-20 w-20 shrink-0 overflow-hidden rounded-lg bg-muted">
                                            {item.image ? (
                                                <img
                                                    src={item.image}
                                                    alt={
                                                        item.product_name ?? ''
                                                    }
                                                    className="h-full w-full object-cover"
                                                />
                                            ) : null}
                                        </div>
                                        <div className="flex flex-1 flex-col gap-1">
                                            <Link
                                                href={
                                                    item.product_slug
                                                        ? `/products/${item.product_slug}`
                                                        : '#'
                                                }
                                                className="text-sm font-medium hover:underline"
                                            >
                                                {item.product_name}
                                            </Link>
                                            {item.variant_name && (
                                                <p className="text-xs text-muted-foreground">
                                                    {item.variant_name}
                                                </p>
                                            )}
                                            <p className="text-sm font-semibold text-primary">
                                                {formatIDR(item.price)}
                                            </p>
                                            <div className="mt-2 flex items-center gap-2">
                                                <Input
                                                    type="number"
                                                    min={1}
                                                    max={item.stock}
                                                    value={item.quantity}
                                                    onChange={(e) =>
                                                        updateQty(
                                                            item,
                                                            parseInt(
                                                                e.target.value,
                                                            ) || 1,
                                                        )
                                                    }
                                                    className="w-20"
                                                />
                                                <span className="text-xs text-muted-foreground">
                                                    Stok {item.stock}
                                                </span>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    onClick={() => remove(item)}
                                                    className="ml-auto"
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-sm font-semibold">
                                                {formatIDR(item.subtotal)}
                                            </p>
                                        </div>
                                    </CardContent>
                                </Card>
                            ))}
                        </div>

                        <Card className="h-fit">
                            <CardHeader>
                                <CardTitle className="text-base">
                                    Ringkasan Belanja
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                <div className="flex justify-between text-sm">
                                    <span>Subtotal</span>
                                    <span>{formatIDR(subtotal)}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span>Ongkir (flat)</span>
                                    <span>{formatIDR(shipping)}</span>
                                </div>
                                <div className="flex justify-between border-t pt-3 font-semibold">
                                    <span>Total</span>
                                    <span>{formatIDR(total)}</span>
                                </div>
                                <Button asChild className="w-full" size="lg">
                                    <Link href="/checkout/address">
                                        Checkout
                                    </Link>
                                </Button>
                                <Button
                                    variant="outline"
                                    asChild
                                    className="w-full"
                                >
                                    <Link href="/products">Lanjut Belanja</Link>
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                )}
            </div>
        </StorefrontLayout>
    );
}
