import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Trash2, Plus } from 'lucide-react';
import { Pagination } from '@/components/storefront/pagination';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { formatIDR } from '@/lib/format';

type Product = {
    id: number;
    name: string;
    slug: string;
    sku: string;
    price: number;
    stock: number;
    total_stock: number;
    is_active: boolean;
    is_featured: boolean;
    category: { name: string } | null;
    image: string | null;
    variants_count: number;
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    products: Paginated<Product>;
};

export default function AdminProductsIndex({ products }: Props) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin' },
                { title: 'Products', href: '/admin/products' },
            ]}
        >
            <Head title="Admin — Products" />
            <div className="p-4">
                <div className="mb-4 flex items-center justify-between">
                    <h1 className="text-xl font-semibold">Products</h1>
                    <Button asChild>
                        <Link href="/admin/products/create">
                            <Plus className="mr-2 h-4 w-4" /> Tambah Produk
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardContent className="p-0">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="border-b bg-muted/50">
                                    <tr>
                                        <th className="p-3 text-left">Produk</th>
                                        <th className="p-3 text-left">SKU</th>
                                        <th className="p-3 text-right">Harga</th>
                                        <th className="p-3 text-center">Stok</th>
                                        <th className="p-3 text-center">Status</th>
                                        <th className="p-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {products.data.map((p) => (
                                        <tr key={p.id} className="border-b last:border-0">
                                            <td className="p-3">
                                                <div className="flex items-center gap-3">
                                                    <div className="h-10 w-10 shrink-0 overflow-hidden rounded bg-muted">
                                                        {p.image ? (
                                                            <img
                                                                src={p.image}
                                                                alt={p.name}
                                                                className="h-full w-full object-cover"
                                                            />
                                                        ) : null}
                                                    </div>
                                                    <div>
                                                        <p className="font-medium">{p.name}</p>
                                                        <p className="text-xs text-muted-foreground">
                                                            {p.category?.name ?? '—'} · {p.variants_count} varian
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="p-3 font-mono text-xs">{p.sku}</td>
                                            <td className="p-3 text-right">{formatIDR(p.price)}</td>
                                            <td className="p-3 text-center">{p.total_stock}</td>
                                            <td className="p-3 text-center">
                                                {p.is_active ? (
                                                    <Badge variant="secondary">Aktif</Badge>
                                                ) : (
                                                    <Badge variant="destructive">Nonaktif</Badge>
                                                )}
                                            </td>
                                            <td className="p-3">
                                                <div className="flex justify-end gap-1">
                                                    <Button variant="ghost" size="icon" asChild>
                                                        <Link href={`/admin/products/${p.id}/edit`}>
                                                            <Pencil className="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        onClick={() => {
                                                            if (confirm(`Hapus produk "${p.name}"?`)) {
                                                                router.delete(`/admin/products/${p.id}`);
                                                            }
                                                        }}
                                                    >
                                                        <Trash2 className="h-4 w-4 text-destructive" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-4">
                    <Pagination links={products.links} />
                </div>
            </div>
        </AppLayout>
    );
}
