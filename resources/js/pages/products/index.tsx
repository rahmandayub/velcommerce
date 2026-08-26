import { Head, Link, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { Pagination } from '@/components/storefront/pagination';
import { ProductCard } from '@/components/storefront/product-card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import StorefrontLayout from '@/layouts/storefront-layout';
import { formatIDR } from '@/lib/format';

type Product = {
    id: number;
    name: string;
    slug: string;
    price: number;
    compare_price: number | null;
    short_description: string | null;
    is_featured: boolean;
    stock: number;
    total_stock: number;
    category: { id: number; name: string; slug: string } | null;
    image: string | null;
};

type Category = {
    id: number;
    name: string;
    slug: string;
    children: { id: number; name: string; slug: string }[];
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    meta?: unknown;
};

type Props = {
    products: Paginated<Product>;
    categories: Category[];
    wishlistIds?: number[];
    filters: {
        q?: string;
        category?: string;
        min_price?: string;
        max_price?: string;
        sort?: string;
    };
};

export default function ProductsIndex({ products, categories, filters, wishlistIds = [] }: Props) {
    const [q, setQ] = useState(filters.q ?? '');
    const [minPrice, setMinPrice] = useState(filters.min_price ?? '');
    const [maxPrice, setMaxPrice] = useState(filters.max_price ?? '');
    const [sort, setSort] = useState(filters.sort ?? 'latest');
    const [category, setCategory] = useState(filters.category ?? '');

    // debounce search
    useEffect(() => {
        const id = setTimeout(() => {
            if (q !== (filters.q ?? '')) {
                applyFilters({ q });
            }
        }, 400);

        return () => clearTimeout(id);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [q]);

    function applyFilters(overrides: Record<string, string> = {}) {
        const params: Record<string, string> = {};

        const next = {
            q: overrides.q !== undefined ? overrides.q : q,
            category: overrides.category !== undefined ? overrides.category : category,
            min_price: overrides.min_price !== undefined ? overrides.min_price : minPrice,
            max_price: overrides.max_price !== undefined ? overrides.max_price : maxPrice,
            sort: overrides.sort !== undefined ? overrides.sort : sort,
        };

        if (next.q) {
params.q = next.q;
}

        if (next.category) {
params.category = next.category;
}

        if (next.min_price) {
params.min_price = next.min_price;
}

        if (next.max_price) {
params.max_price = next.max_price;
}

        if (next.sort && next.sort !== 'latest') {
params.sort = next.sort;
}

        router.get('/products', params, {
            only: ['products'],
            preserveState: true,
            preserveScroll: true,
        });
    }

    return (
        <StorefrontLayout>
            <Head title="Katalog Produk" />
            <div className="mx-auto max-w-7xl px-4 py-8">
                <div className="mb-6">
                    <h1 className="text-2xl font-bold tracking-tight">Katalog Produk</h1>
                    <p className="text-sm text-muted-foreground">
                        Temukan produk terbaik dengan harga terjangkau.
                    </p>
                </div>

                <div className="grid gap-6 lg:grid-cols-[260px_1fr]">
                    {/* Sidebar filters */}
                    <aside className="space-y-6 rounded-xl border bg-card p-4 h-fit">
                        <div className="space-y-2">
                            <Label htmlFor="q">Cari</Label>
                            <Input
                                id="q"
                                placeholder="Nama, SKU, deskripsi..."
                                value={q}
                                onChange={(e) => setQ(e.target.value)}
                            />
                        </div>

                        <div className="space-y-2">
                            <Label>Kategori</Label>
                            <div className="flex flex-wrap gap-1.5">
                                <Badge
                                    variant={!category ? 'default' : 'outline'}
                                    className="cursor-pointer"
                                    onClick={() => {
                                        setCategory('');
                                        applyFilters({ category: '' });
                                    }}
                                >
                                    Semua
                                </Badge>
                                {categories.map((cat) => (
                                    <Badge
                                        key={cat.id}
                                        variant={category === cat.slug ? 'default' : 'outline'}
                                        className="cursor-pointer"
                                        onClick={() => {
                                            setCategory(cat.slug);
                                            applyFilters({ category: cat.slug });
                                        }}
                                    >
                                        {cat.name}
                                    </Badge>
                                ))}
                            </div>
                            {categories
                                .filter((c) => c.children.length > 0)
                                .map((cat) => (
                                    <div key={`child-${cat.id}`} className="ml-2 mt-2 space-y-1">
                                        <p className="text-xs font-medium text-muted-foreground">
                                            {cat.name} — sub:
                                        </p>
                                        <div className="flex flex-wrap gap-1">
                                            {cat.children.map((child) => (
                                                <Badge
                                                    key={child.id}
                                                    variant={
                                                        category === child.slug ? 'default' : 'secondary'
                                                    }
                                                    className="cursor-pointer text-xs"
                                                    onClick={() => {
                                                        setCategory(child.slug);
                                                        applyFilters({ category: child.slug });
                                                    }}
                                                >
                                                    {child.name}
                                                </Badge>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                        </div>

                        <div className="space-y-2">
                            <Label>Harga</Label>
                            <div className="flex gap-2">
                                <Input
                                    placeholder="Min"
                                    type="number"
                                    value={minPrice}
                                    onChange={(e) => setMinPrice(e.target.value)}
                                />
                                <Input
                                    placeholder="Max"
                                    type="number"
                                    value={maxPrice}
                                    onChange={(e) => setMaxPrice(e.target.value)}
                                />
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Contoh: {formatIDR(50000)} — {formatIDR(500000)}
                            </p>
                            <Button
                                size="sm"
                                variant="outline"
                                className="w-full"
                                onClick={() => applyFilters()}
                            >
                                Terapkan Harga
                            </Button>
                        </div>

                        <div className="space-y-2">
                            <Label>Urutkan</Label>
                            <Select
                                value={sort}
                                onValueChange={(v) => {
                                    setSort(v);
                                    applyFilters({ sort: v });
                                }}
                            >
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="latest">Terbaru</SelectItem>
                                    <SelectItem value="price_asc">Harga: Rendah ke Tinggi</SelectItem>
                                    <SelectItem value="price_desc">Harga: Tinggi ke Rendah</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </aside>

                    {/* Products grid */}
                    <div className="space-y-6">
                        {products.data.length === 0 ? (
                            <div className="rounded-xl border bg-card p-12 text-center">
                                <p className="text-muted-foreground">Tidak ada produk ditemukan.</p>
                                <Button variant="outline" className="mt-4" asChild>
                                    <Link href="/products">Reset filter</Link>
                                </Button>
                            </div>
                        ) : (
                            <>
                                <p className="text-sm text-muted-foreground">
                                    Menampilkan {products.data.length} produk
                                </p>
                                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {products.data.map((p) => (
                                        <ProductCard
                                            key={p.id}
                                            product={p}
                                            isWishlisted={wishlistIds.includes(p.id)}
                                        />
                                    ))}
                                </div>
                                <Pagination links={products.links} />
                            </>
                        )}
                    </div>
                </div>
            </div>
        </StorefrontLayout>
    );
}
