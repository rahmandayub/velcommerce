import { Link } from '@inertiajs/react';
import { WishlistButton } from '@/components/storefront/wishlist-button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { formatIDR } from '@/lib/format';

type ProductCardProps = {
    product: {
        id: number;
        name: string;
        slug: string;
        price: number;
        compare_price?: number | null;
        short_description?: string | null;
        image?: string | null;
        stock: number;
        total_stock: number;
        is_featured?: boolean;
        average_rating?: number;
        reviews_count?: number;
    };
    isWishlisted?: boolean;
};

export function ProductCard({ product, isWishlisted = false }: ProductCardProps) {
    const outOfStock = product.total_stock <= 0;
    const hasRating = (product.reviews_count ?? 0) > 0;

    return (
        <Card className="group relative overflow-hidden transition hover:shadow-lg">
            <div className="absolute right-2 top-2 z-10">
                <WishlistButton productId={product.id} isWishlisted={isWishlisted} />
            </div>
            <Link
                href={`/products/${product.slug}`}
                className="block"
                prefetch
            >
                <div className="relative aspect-square overflow-hidden bg-muted">
                    {product.image ? (
                        <img
                            src={product.image}
                            alt={product.name}
                            className="h-full w-full object-cover transition group-hover:scale-105"
                            loading="lazy"
                        />
                    ) : (
                        <div className="flex h-full w-full items-center justify-center text-muted-foreground">
                            No image
                        </div>
                    )}
                    {product.is_featured && (
                        <Badge className="absolute top-2 left-2">Featured</Badge>
                    )}
                    {outOfStock && (
                        <div className="absolute inset-0 flex items-center justify-center bg-black/50 text-sm font-medium text-white">
                            Stok Habis
                        </div>
                    )}
                </div>
                <CardContent className="space-y-2 p-4">
                    <h3 className="line-clamp-2 text-sm font-medium leading-snug">
                        {product.name}
                    </h3>
                    {hasRating && (
                        <div className="flex items-center gap-1 text-xs text-muted-foreground">
                            <span className="text-yellow-500">★</span>
                            <span>{product.average_rating?.toFixed(1)}</span>
                            <span>({product.reviews_count})</span>
                        </div>
                    )}
                    <div className="flex items-center gap-2">
                        <span className="font-semibold text-primary">
                            {formatIDR(product.price)}
                        </span>
                        {product.compare_price ? (
                            <span className="text-xs text-muted-foreground line-through">
                                {formatIDR(product.compare_price)}
                            </span>
                        ) : null}
                    </div>
                    <div>
                        {outOfStock ? (
                            <Badge variant="destructive" className="text-xs">
                                Habis
                            </Badge>
                        ) : (
                            <Badge variant="secondary" className="text-xs">
                                Stok {product.total_stock}
                            </Badge>
                        )}
                    </div>
                </CardContent>
            </Link>
        </Card>
    );
}
