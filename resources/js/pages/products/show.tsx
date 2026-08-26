import { router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { SeoHead } from '@/components/seo-head';
import { Pagination } from '@/components/storefront/pagination';
import { ProductGallery } from '@/components/storefront/product-gallery';
import { ReviewCard } from '@/components/storefront/review-card';
import { StarRating } from '@/components/storefront/star-rating';
import { VariantSelector } from '@/components/storefront/variant-selector';
import { WishlistButton } from '@/components/storefront/wishlist-button';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import StorefrontLayout from '@/layouts/storefront-layout';
import { formatIDR } from '@/lib/format';

type Variant = {
    id: number;
    sku: string;
    name: string | null;
    price: number | null;
    effective_price: number;
    stock: number;
    attributes: Record<string, string> | null;
    is_active: boolean;
};

type Review = {
    id: number;
    rating: number;
    title: string | null;
    body: string | null;
    created_at: string;
    user: { name: string | null } | null;
};

type PaginatedReview = {
    data: Review[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Seo = {
    title: string;
    description: string;
    canonical: string;
    image: string | null;
    type: string;
};

type Props = {
    product: {
        id: number;
        name: string;
        slug: string;
        description: string;
        short_description: string | null;
        price: number;
        compare_price: number | null;
        sku: string;
        stock: number;
        total_stock: number;
        is_active: boolean;
        average_rating: number;
        reviews_count: number;
        category: { id: number; name: string; slug: string } | null;
        images: {
            id: number;
            url: string;
            is_primary: boolean;
            alt: string | null;
        }[];
        variants: Variant[];
        reviews: PaginatedReview;
        can_review: boolean;
        user_review: {
            id: number;
            rating: number;
            title: string | null;
            body: string | null;
        } | null;
    };
    seo: Seo;
    jsonLd: Record<string, unknown>;
    breadcrumbLd: Record<string, unknown>;
};

export default function ProductShow({
    product,
    seo,
    jsonLd,
    breadcrumbLd,
}: Props) {
    const hasVariants = product.variants.length > 0;
    const [selectedVariantId, setSelectedVariantId] = useState<number | null>(
        hasVariants ? product.variants[0].id : null,
    );
    const [qty, setQty] = useState(1);

    const selectedVariant =
        product.variants.find((v) => v.id === selectedVariantId) ?? null;
    const effectivePrice = selectedVariant
        ? selectedVariant.effective_price
        : product.price;
    const availableStock = selectedVariant
        ? selectedVariant.stock
        : product.stock;
    const outOfStock = availableStock <= 0;

    const form = useForm({
        product_id: product.id,
        variant_id: selectedVariantId,
        quantity: qty,
    });

    function handleAddToCart() {
        form.transform((data) => ({
            ...data,
            variant_id: selectedVariantId,
            quantity: qty,
        }));

        // Wayfinder or plain post: /cart/items
        router.post(
            '/cart/items',
            {
                product_id: product.id,
                variant_id: selectedVariantId,
                quantity: qty,
            },
            {
                preserveScroll: true,
            },
        );
    }

    return (
        <StorefrontLayout>
            <SeoHead
                title={seo.title}
                description={seo.description}
                canonical={seo.canonical}
                image={seo.image}
                type={seo.type}
                jsonLd={jsonLd}
                breadcrumbLd={breadcrumbLd}
            />
            <div className="mx-auto max-w-7xl px-4 py-8">
                <div className="grid gap-8 lg:grid-cols-2">
                    <ProductGallery
                        images={product.images}
                        productName={product.name}
                    />

                    <div className="space-y-6">
                        {product.category && (
                            <Badge variant="secondary">
                                {product.category.name}
                            </Badge>
                        )}
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">
                                {product.name}
                            </h1>
                            <p className="mt-1 text-sm text-muted-foreground">
                                SKU: {selectedVariant?.sku ?? product.sku}
                            </p>
                        </div>

                        <div className="flex items-baseline gap-3">
                            <span className="text-2xl font-bold text-primary">
                                {formatIDR(effectivePrice)}
                            </span>
                            {product.compare_price && (
                                <span className="text-sm text-muted-foreground line-through">
                                    {formatIDR(product.compare_price)}
                                </span>
                            )}
                        </div>

                        {product.short_description && (
                            <p className="text-muted-foreground">
                                {product.short_description}
                            </p>
                        )}

                        <VariantSelector
                            variants={product.variants}
                            selectedId={selectedVariantId}
                            onSelect={setSelectedVariantId}
                        />

                        <div className="flex items-center gap-3">
                            <div className="flex items-center gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    onClick={() =>
                                        setQty((v) => Math.max(1, v - 1))
                                    }
                                    disabled={qty <= 1}
                                >
                                    −
                                </Button>
                                <Input
                                    type="number"
                                    min={1}
                                    max={availableStock}
                                    value={qty}
                                    onChange={(e) =>
                                        setQty(
                                            Math.max(
                                                1,
                                                parseInt(e.target.value) || 1,
                                            ),
                                        )
                                    }
                                    className="w-20 text-center"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    onClick={() =>
                                        setQty((v) =>
                                            Math.min(
                                                availableStock || 99,
                                                v + 1,
                                            ),
                                        )
                                    }
                                    disabled={
                                        availableStock !== 0 &&
                                        qty >= availableStock
                                    }
                                >
                                    +
                                </Button>
                            </div>
                            <span className="text-sm text-muted-foreground">
                                Stok: {availableStock}
                            </span>
                        </div>

                        <div className="flex items-center gap-3">
                            <Button
                                size="lg"
                                className="flex-1"
                                disabled={
                                    outOfStock ||
                                    (hasVariants && !selectedVariantId)
                                }
                                onClick={handleAddToCart}
                            >
                                {outOfStock
                                    ? 'Stok Habis'
                                    : 'Tambah ke Keranjang'}
                            </Button>
                            <WishlistButton
                                productId={product.id}
                                isWishlisted={false}
                            />
                        </div>

                        {hasVariants && !selectedVariantId && (
                            <p className="text-sm text-destructive">
                                Pilih varian terlebih dahulu.
                            </p>
                        )}

                        <div className="prose prose-sm dark:prose-invert max-w-none">
                            <h3>Deskripsi</h3>
                            <p className="whitespace-pre-line text-muted-foreground">
                                {product.description}
                            </p>
                        </div>
                    </div>
                </div>

                {/* Reviews */}
                <div className="mt-12">
                    <div className="mb-4 flex items-center justify-between">
                        <h2 className="text-xl font-semibold">
                            Ulasan ({product.reviews_count})
                        </h2>
                        <div className="flex items-center gap-2">
                            <StarRating
                                value={product.average_rating}
                                size={18}
                            />
                            <span className="text-sm text-muted-foreground">
                                {product.average_rating > 0
                                    ? `${product.average_rating.toFixed(1)} / 5`
                                    : 'Belum ada ulasan'}
                            </span>
                        </div>
                    </div>

                    <ReviewForm
                        productId={product.id}
                        canReview={product.can_review}
                        userReview={product.user_review}
                    />

                    <div className="mt-4">
                        {product.reviews.data.length === 0 ? (
                            <p className="text-sm text-muted-foreground">
                                Jadilah yang pertama memberikan ulasan.
                            </p>
                        ) : (
                            product.reviews.data.map((review) => (
                                <ReviewCard key={review.id} review={review} />
                            ))
                        )}
                    </div>

                    {product.reviews.links.length > 3 && (
                        <div className="mt-6">
                            <Pagination links={product.reviews.links} />
                        </div>
                    )}
                </div>
            </div>
        </StorefrontLayout>
    );
}

type ReviewFormProps = {
    productId: number;
    canReview: boolean;
    userReview: Props['product']['user_review'];
};

function ReviewForm({ productId, canReview, userReview }: ReviewFormProps) {
    const [rating, setRating] = useState(userReview?.rating ?? 0);

    const form = useForm({
        product_id: productId,
        rating,
        title: userReview?.title ?? '',
        body: userReview?.body ?? '',
    });

    if (!canReview && !userReview) {
        return (
            <div className="rounded-md border border-dashed p-4 text-sm text-muted-foreground">
                Beli produk ini untuk dapat memberikan ulasan.
            </div>
        );
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        form.setData('rating', rating);

        form.post('/reviews', {
            preserveScroll: true,
            onSuccess: () => {
                if (!userReview) {
                    form.reset('title', 'body');
                    setRating(0);
                }
            },
        });
    }

    return (
        <form onSubmit={submit} className="space-y-3 rounded-md border p-4">
            <div className="flex items-center gap-2">
                <span className="text-sm font-medium">Rating:</span>
                <StarRating value={rating} editable onChange={setRating} />
            </div>
            <Input
                placeholder="Judul ulasan (opsional)"
                value={form.data.title}
                onChange={(e) => form.setData('title', e.target.value)}
            />
            <Textarea
                placeholder="Tulis ulasan Anda..."
                value={form.data.body}
                onChange={(e) => form.setData('body', e.target.value)}
            />
            {form.errors && Object.keys(form.errors).length > 0 && (
                <ul className="text-sm text-destructive">
                    {Object.values(form.errors).map((msg: unknown, i) => (
                        <li key={i}>{String(msg)}</li>
                    ))}
                </ul>
            )}
            <Button type="submit" disabled={form.processing || rating === 0}>
                {userReview ? 'Perbarui Ulasan' : 'Kirim Ulasan'}
            </Button>
        </form>
    );
}
