import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';

type CategoryOption = { id: number; name: string };
type ProductImage = {
    id: number;
    url: string;
    is_primary: boolean;
    sort_order: number;
};
type Variant = {
    id?: number;
    sku: string;
    name: string | null;
    price: number | null;
    stock: number;
    attributes: Record<string, string> | null;
    is_active: boolean;
};

type Product = {
    id: number;
    name: string;
    slug: string;
    description: string;
    short_description: string | null;
    price: number;
    compare_price: number | null;
    cost: number | null;
    sku: string;
    barcode: string | null;
    stock: number;
    category_id: number | null;
    is_active: boolean;
    is_featured: boolean;
    weight: number | null;
    meta_title: string | null;
    meta_description: string | null;
    images: ProductImage[];
    variants: Variant[];
} | null;

type Props = {
    product: Product;
    categories: CategoryOption[];
};

export default function AdminProductForm({ product, categories }: Props) {
    const isEdit = !!product;

    const [form, setForm] = useState({
        name: product?.name ?? '',
        description: product?.description ?? '',
        short_description: product?.short_description ?? '',
        price: product?.price?.toString() ?? '',
        compare_price: product?.compare_price?.toString() ?? '',
        sku: product?.sku ?? '',
        stock: product?.stock?.toString() ?? '0',
        category_id: product?.category_id?.toString() ?? '',
        is_active: product?.is_active ?? true,
        is_featured: product?.is_featured ?? false,
    });

    const [variants, setVariants] = useState<Variant[]>(
        product?.variants?.length ? product.variants : [],
    );

    const [newFiles, setNewFiles] = useState<File[]>([]);
    const [previews, setPreviews] = useState<string[]>([]);

    function handleFiles(e: React.ChangeEvent<HTMLInputElement>) {
        const files = Array.from(e.target.files ?? []);
        setNewFiles(files);
        setPreviews(files.map((f) => URL.createObjectURL(f)));
    }

    function addVariant() {
        setVariants((prev) => [
            ...prev,
            {
                sku: '',
                name: '',
                price: null,
                stock: 0,
                attributes: null,
                is_active: true,
            },
        ]);
    }

    function updateVariant(idx: number, patch: Partial<Variant>) {
        setVariants((prev) =>
            prev.map((v, i) => (i === idx ? { ...v, ...patch } : v)),
        );
    }

    function removeVariant(idx: number) {
        setVariants((prev) => prev.filter((_, i) => i !== idx));
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();

        const fd = new FormData();
        fd.append('name', form.name);
        fd.append('description', form.description);
        fd.append('short_description', form.short_description);
        fd.append('price', form.price);

        if (form.compare_price) {
            fd.append('compare_price', form.compare_price);
        }

        fd.append('sku', form.sku);
        fd.append('stock', form.stock);

        if (form.category_id) {
            fd.append('category_id', form.category_id);
        }

        fd.append('is_active', form.is_active ? '1' : '0');
        fd.append('is_featured', form.is_featured ? '1' : '0');

        newFiles.forEach((f) => fd.append('images[]', f));

        variants.forEach((v, i) => {
            if (v.id) {
                fd.append(`variants[${i}][id]`, String(v.id));
            }

            fd.append(`variants[${i}][sku]`, v.sku);

            if (v.name) {
                fd.append(`variants[${i}][name]`, v.name);
            }

            if (v.price !== null && v.price !== undefined) {
                fd.append(`variants[${i}][price]`, String(v.price));
            }

            fd.append(`variants[${i}][stock]`, String(v.stock));

            if (v.attributes) {
                fd.append(
                    `variants[${i}][attributes]`,
                    JSON.stringify(v.attributes),
                );
            }

            fd.append(`variants[${i}][is_active]`, v.is_active ? '1' : '0');
        });

        if (isEdit) {
            fd.append('_method', 'PUT');
            router.post(`/admin/products/${product!.id}`, fd, {
                forceFormData: true,
            });
        } else {
            router.post('/admin/products', fd, { forceFormData: true });
        }
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin' },
                { title: 'Products', href: '/admin/products' },
                { title: isEdit ? 'Edit' : 'Create', href: '#' },
            ]}
        >
            <Head title={isEdit ? `Edit ${product!.name}` : 'Create Product'} />
            <div className="mx-auto max-w-4xl p-4">
                <h1 className="mb-4 text-xl font-semibold">
                    {isEdit ? 'Edit Produk' : 'Tambah Produk'}
                </h1>

                <form onSubmit={submit} className="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base">
                                Informasi Dasar
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-1 sm:col-span-2">
                                    <Label>Nama *</Label>
                                    <Input
                                        value={form.name}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                name: e.target.value,
                                            })
                                        }
                                        required
                                    />
                                </div>
                                <div className="space-y-1 sm:col-span-2">
                                    <Label>Deskripsi *</Label>
                                    <textarea
                                        value={form.description}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                description: e.target.value,
                                            })
                                        }
                                        required
                                        rows={4}
                                        className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                                    />
                                </div>
                                <div className="space-y-1 sm:col-span-2">
                                    <Label>Short Description</Label>
                                    <Input
                                        value={form.short_description}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                short_description:
                                                    e.target.value,
                                            })
                                        }
                                    />
                                </div>
                                <div className="space-y-1">
                                    <Label>Harga *</Label>
                                    <Input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={form.price}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                price: e.target.value,
                                            })
                                        }
                                        required
                                    />
                                </div>
                                <div className="space-y-1">
                                    <Label>Compare Price</Label>
                                    <Input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={form.compare_price}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                compare_price: e.target.value,
                                            })
                                        }
                                    />
                                </div>
                                <div className="space-y-1">
                                    <Label>SKU *</Label>
                                    <Input
                                        value={form.sku}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                sku: e.target.value,
                                            })
                                        }
                                        required
                                    />
                                </div>
                                <div className="space-y-1">
                                    <Label>Stok *</Label>
                                    <Input
                                        type="number"
                                        min="0"
                                        value={form.stock}
                                        onChange={(e) =>
                                            setForm({
                                                ...form,
                                                stock: e.target.value,
                                            })
                                        }
                                        required
                                    />
                                </div>
                                <div className="space-y-1">
                                    <Label>Kategori</Label>
                                    <Select
                                        value={form.category_id}
                                        onValueChange={(v) =>
                                            setForm({ ...form, category_id: v })
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Pilih kategori" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {categories.map((c) => (
                                                <SelectItem
                                                    key={c.id}
                                                    value={String(c.id)}
                                                >
                                                    {c.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div className="space-y-1">
                                    <Label>Status</Label>
                                    <Select
                                        value={form.is_active ? '1' : '0'}
                                        onValueChange={(v) =>
                                            setForm({
                                                ...form,
                                                is_active: v === '1',
                                            })
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="1">
                                                Aktif
                                            </SelectItem>
                                            <SelectItem value="0">
                                                Nonaktif
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base">
                                Gambar (maks 5, jpg/png/webp, 2MB)
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {product?.images?.length ? (
                                <div className="flex flex-wrap gap-2">
                                    {product.images.map((img) => (
                                        <div
                                            key={img.id}
                                            className="relative h-20 w-20 overflow-hidden rounded border"
                                        >
                                            <img
                                                src={img.url}
                                                alt=""
                                                className="h-full w-full object-cover"
                                            />
                                            {img.is_primary && (
                                                <span className="absolute bottom-0 left-0 bg-primary px-1 text-[10px] text-primary-foreground">
                                                    Primary
                                                </span>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            ) : null}
                            <Input
                                type="file"
                                multiple
                                accept="image/*"
                                onChange={handleFiles}
                            />
                            {previews.length > 0 && (
                                <div className="flex flex-wrap gap-2">
                                    {previews.map((src, i) => (
                                        <img
                                            key={i}
                                            src={src}
                                            alt={`preview ${i}`}
                                            className="h-20 w-20 rounded border object-cover"
                                        />
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between">
                            <CardTitle className="text-base">
                                Varian (opsional)
                            </CardTitle>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                onClick={addVariant}
                            >
                                + Tambah Varian
                            </Button>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {variants.length === 0 && (
                                <p className="text-sm text-muted-foreground">
                                    Belum ada varian. Tambahkan jika produk
                                    memiliki warna/ukuran.
                                </p>
                            )}
                            {variants.map((v, idx) => (
                                <div
                                    key={idx}
                                    className="grid gap-2 rounded-lg border p-3 sm:grid-cols-4"
                                >
                                    <div className="space-y-1">
                                        <Label>SKU *</Label>
                                        <Input
                                            value={v.sku}
                                            onChange={(e) =>
                                                updateVariant(idx, {
                                                    sku: e.target.value,
                                                })
                                            }
                                            placeholder="VAR-001"
                                        />
                                    </div>
                                    <div className="space-y-1">
                                        <Label>Nama</Label>
                                        <Input
                                            value={v.name ?? ''}
                                            onChange={(e) =>
                                                updateVariant(idx, {
                                                    name: e.target.value,
                                                })
                                            }
                                            placeholder="Merah - M"
                                        />
                                    </div>
                                    <div className="space-y-1">
                                        <Label>Harga</Label>
                                        <Input
                                            type="number"
                                            step="0.01"
                                            value={v.price ?? ''}
                                            onChange={(e) =>
                                                updateVariant(idx, {
                                                    price: e.target.value
                                                        ? parseFloat(
                                                              e.target.value,
                                                          )
                                                        : null,
                                                })
                                            }
                                            placeholder="Kosong = ikut produk"
                                        />
                                    </div>
                                    <div className="space-y-1">
                                        <Label>Stok *</Label>
                                        <Input
                                            type="number"
                                            min="0"
                                            value={v.stock}
                                            onChange={(e) =>
                                                updateVariant(idx, {
                                                    stock:
                                                        parseInt(
                                                            e.target.value,
                                                        ) || 0,
                                                })
                                            }
                                        />
                                    </div>
                                    <div className="space-y-1 sm:col-span-3">
                                        <Label>
                                            Attributes JSON (cth:{' '}
                                            {`{"color":"Merah","size":"M"}`})
                                        </Label>
                                        <Input
                                            value={
                                                v.attributes
                                                    ? JSON.stringify(
                                                          v.attributes,
                                                      )
                                                    : ''
                                            }
                                            onChange={(e) => {
                                                try {
                                                    const parsed = e.target
                                                        .value
                                                        ? JSON.parse(
                                                              e.target.value,
                                                          )
                                                        : null;
                                                    updateVariant(idx, {
                                                        attributes: parsed,
                                                    });
                                                } catch {
                                                    // ignore parse error while typing; keep raw? store as null until valid
                                                }
                                            }}
                                            placeholder='{"color":"Merah","size":"M"}'
                                        />
                                    </div>
                                    <div className="flex items-end">
                                        <Button
                                            type="button"
                                            variant="destructive"
                                            size="sm"
                                            onClick={() => removeVariant(idx)}
                                        >
                                            Hapus
                                        </Button>
                                    </div>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <div className="flex gap-2">
                        <Button type="submit">
                            {isEdit ? 'Simpan Perubahan' : 'Buat Produk'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <Link href="/admin/products">Batal</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
