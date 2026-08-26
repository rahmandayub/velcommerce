import { Head, Link, router } from '@inertiajs/react';
import { Heart, ShoppingCart } from 'lucide-react';
import { Pagination } from '@/components/storefront/pagination';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import StorefrontLayout from '@/layouts/storefront-layout';
import { formatIDR } from '@/lib/format';
import { destroy as wishlistDestroy } from '@/routes/wishlist';

type WishlistItem = {
    id: number;
    product: {
        id: number;
        name: string;
        slug: string;
        price: number;
        compare_price: number | null;
        stock: number;
        is_active: boolean;
        image: string | null;
        category: { name: string } | null;
    };
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    items: Paginated<WishlistItem>;
};

export default function WishlistIndex({ items }: Props) {
    function moveToCart(item: WishlistItem) {
        router.post(
            '/cart/items',
            { product_id: item.product.id, quantity: 1 },
            {
                preserveScroll: true,
                onSuccess: () => {
                    router.delete(wishlistDestroy({ wishlist: item.id }).url, {
                        preserveScroll: true,
                    });
                },
            },
        );
    }

    function remove(id: number) {
        router.delete(wishlistDestroy({ wishlist: id }).url, {
            preserveScroll: true,
        });
    }

    return (
        <StorefrontLayout>
            <Head title="Wishlist" />
            <div className="mx-auto max-w-5xl px-4 py-8">
                <h1 className="mb-6 flex items-center gap-2 text-2xl font-bold">
                    <Heart className="h-6 w-6" /> Wishlist
                </h1>

                {items.data.length === 0 ? (
                    <div className="rounded-lg border border-dashed py-16 text-center">
                        <p className="text-muted-foreground">
                            Wishlist Anda masih kosong.
                        </p>
                        <Button asChild className="mt-4">
                            <Link href="/products">Jelajahi Produk</Link>
                        </Button>
                    </div>
                ) : (
                    <div className="grid gap-4 sm:grid-cols-2">
                        {items.data.map((item) => (
                            <Card key={item.id}>
                                <CardContent className="flex gap-4 p-4">
                                    <Link href={`/products/${item.product.slug}`} className="shrink-0">
                                        {item.product.image ? (
                                            <img
                                                src={item.product.image}
                                                alt={item.product.name}
                                                className="h-24 w-24 rounded-md object-cover"
                                                loading="lazy"
                                            />
                                        ) : (
                                            <div className="flex h-24 w-24 items-center justify-center rounded-md bg-muted text-xs text-muted-foreground">
                                                No image
                                            </div>
                                        )}
                                    </Link>
                                    <div className="flex flex-1 flex-col">
                                        <Link
                                            href={`/products/${item.product.slug}`}
                                            className="line-clamp-2 font-medium hover:underline"
                                        >
                                            {item.product.name}
                                        </Link>
                                        {item.product.category && (
                                            <span className="text-xs text-muted-foreground">
                                                {item.product.category.name}
                                            </span>
                                        )}
                                        <span className="mt-1 font-semibold text-primary">
                                            {formatIDR(item.product.price)}
                                        </span>
                                        {!item.product.is_active && (
                                            <Badge variant="destructive" className="mt-1 w-fit text-xs">
                                                Tidak tersedia
                                            </Badge>
                                        )}
                                        <div className="mt-auto flex gap-2 pt-2">
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                disabled={!item.product.is_active}
                                                onClick={() => moveToCart(item)}
                                            >
                                                <ShoppingCart className="h-4 w-4" /> Keranjang
                                            </Button>
                                            <Button
                                                size="sm"
                                                variant="ghost"
                                                onClick={() => remove(item.id)}
                                            >
                                                Hapus
                                            </Button>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}

                {items.links.length > 3 && (
                    <div className="mt-6">
                        <Pagination links={items.links} />
                    </div>
                )}
            </div>
        </StorefrontLayout>
    );
}
