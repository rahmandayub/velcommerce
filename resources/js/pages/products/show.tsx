import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ProductGallery } from '@/components/storefront/product-gallery';
import { VariantSelector } from '@/components/storefront/variant-selector';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
        category: { id: number; name: string; slug: string } | null;
        images: { id: number; url: string; is_primary: boolean; alt: string | null }[];
        variants: Variant[];
    };
};

export default function ProductShow({ product }: Props) {
    const hasVariants = product.variants.length > 0;
    const [selectedVariantId, setSelectedVariantId] = useState<number | null>(
        hasVariants ? product.variants[0].id : null,
    );
    const [qty, setQty] = useState(1);

    const selectedVariant = product.variants.find((v) => v.id === selectedVariantId) ?? null;
    const effectivePrice = selectedVariant ? selectedVariant.effective_price : product.price;
    const availableStock = selectedVariant ? selectedVariant.stock : product.stock;
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
        router.post('/cart/items', {
            product_id: product.id,
            variant_id: selectedVariantId,
            quantity: qty,
        }, {
            preserveScroll: true,
        });
    }

    return (
        <StorefrontLayout>
            <Head title={product.name} />
            <div className="mx-auto max-w-7xl px-4 py-8">
                <div className="grid gap-8 lg:grid-cols-2">
                    <ProductGallery images={product.images} productName={product.name} />

                    <div className="space-y-6">
                        {product.category && (
                            <Badge variant="secondary">{product.category.name}</Badge>
                        )}
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">{product.name}</h1>
                            <p className="mt-1 text-sm text-muted-foreground">SKU: {selectedVariant?.sku ?? product.sku}</p>
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
                            <p className="text-muted-foreground">{product.short_description}</p>
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
                                    onClick={() => setQty((v) => Math.max(1, v - 1))}
                                    disabled={qty <= 1}
                                >
                                    −
                                </Button>
                                <Input
                                    type="number"
                                    min={1}
                                    max={availableStock}
                                    value={qty}
                                    onChange={(e) => setQty(Math.max(1, parseInt(e.target.value) || 1))}
                                    className="w-20 text-center"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    onClick={() => setQty((v) => Math.min(availableStock || 99, v + 1))}
                                    disabled={availableStock !== 0 && qty >= availableStock}
                                >
                                    +
                                </Button>
                            </div>
                            <span className="text-sm text-muted-foreground">
                                Stok: {availableStock}
                            </span>
                        </div>

                        <Button
                            size="lg"
                            className="w-full"
                            disabled={outOfStock || (hasVariants && !selectedVariantId)}
                            onClick={handleAddToCart}
                        >
                            {outOfStock ? 'Stok Habis' : 'Tambah ke Keranjang'}
                        </Button>

                        {hasVariants && !selectedVariantId && (
                            <p className="text-sm text-destructive">Pilih varian terlebih dahulu.</p>
                        )}

                        <div className="prose prose-sm max-w-none dark:prose-invert">
                            <h3>Deskripsi</h3>
                            <p className="whitespace-pre-line text-muted-foreground">
                                {product.description}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </StorefrontLayout>
    );
}
