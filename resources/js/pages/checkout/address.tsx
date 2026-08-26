import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import StorefrontLayout from '@/layouts/storefront-layout';
import { formatIDR } from '@/lib/format';

type Address = {
    id: number;
    label: string;
    recipient_name: string;
    phone: string;
    full_address: string;
    street: string;
    village: string | null;
    district: string;
    city: string;
    province: string;
    postal_code: string;
    is_default: boolean;
};

type CartItem = {
    id: number;
    product_name: string | null;
    variant_name: string | null;
    price: number;
    quantity: number;
    subtotal: number;
    image: string | null;
};

type Props = {
    addresses: Address[];
    cart: {
        items: CartItem[];
        subtotal: number;
        shipping_cost: number;
    };
};

export default function CheckoutAddress({ addresses, cart }: Props) {
    const [selectedId, setSelectedId] = useState<number | null>(
        addresses.find((a) => a.is_default)?.id ?? addresses[0]?.id ?? null,
    );
    const [showForm, setShowForm] = useState(addresses.length === 0);

    const form = useForm({
        label: 'Rumah',
        recipient_name: '',
        phone: '',
        street: '',
        village: '',
        district: '',
        city: '',
        province: '',
        postal_code: '',
        is_default: false as boolean,
    });

    function submitAddress(e: React.FormEvent) {
        e.preventDefault();
        form.post('/addresses', {
            preserveScroll: true,
            onSuccess: () => {
                setShowForm(false);
                form.reset();
            },
        });
    }

    return (
        <StorefrontLayout>
            <Head title="Checkout — Alamat" />
            <div className="mx-auto max-w-5xl px-4 py-8">
                <div className="mb-6">
                    <h1 className="text-2xl font-bold">Checkout — Pilih Alamat</h1>
                    <p className="text-sm text-muted-foreground">Langkah 1 dari 2</p>
                </div>

                <div className="grid gap-6 lg:grid-cols-[1fr_360px]">
                    <div className="space-y-4">
                        {addresses.map((addr) => (
                            <Card
                                key={addr.id}
                                className={
                                    selectedId === addr.id ? 'border-primary ring-1 ring-primary' : ''
                                }
                            >
                                <CardContent className="flex gap-3 p-4">
                                    <input
                                        type="radio"
                                        name="address"
                                        checked={selectedId === addr.id}
                                        onChange={() => setSelectedId(addr.id)}
                                        className="mt-1"
                                    />
                                    <div className="flex-1">
                                        <p className="text-sm font-semibold">
                                            {addr.label} {addr.is_default && <span className="text-primary">(Utama)</span>}
                                        </p>
                                        <p className="text-sm">{addr.recipient_name} — {addr.phone}</p>
                                        <p className="text-sm text-muted-foreground">{addr.full_address}</p>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}

                        {!showForm ? (
                            <Button variant="outline" onClick={() => setShowForm(true)}>
                                + Tambah Alamat Baru
                            </Button>
                        ) : (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-base">Alamat Baru</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <form onSubmit={submitAddress} className="space-y-3">
                                        <div className="grid gap-3 sm:grid-cols-2">
                                            <div className="space-y-1">
                                                <Label>Label</Label>
                                                <Input value={form.data.label} onChange={(e) => form.setData('label', e.target.value)} required />
                                            </div>
                                            <div className="space-y-1">
                                                <Label>Nama Penerima</Label>
                                                <Input value={form.data.recipient_name} onChange={(e) => form.setData('recipient_name', e.target.value)} required />
                                            </div>
                                            <div className="space-y-1">
                                                <Label>Telepon</Label>
                                                <Input value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value)} required />
                                            </div>
                                            <div className="space-y-1">
                                                <Label>Kode Pos</Label>
                                                <Input value={form.data.postal_code} onChange={(e) => form.setData('postal_code', e.target.value)} required />
                                            </div>
                                        </div>
                                        <div className="space-y-1">
                                            <Label>Jalan</Label>
                                            <Input value={form.data.street} onChange={(e) => form.setData('street', e.target.value)} required />
                                        </div>
                                        <div className="grid gap-3 sm:grid-cols-2">
                                            <div className="space-y-1">
                                                <Label>Desa/Kelurahan</Label>
                                                <Input value={form.data.village} onChange={(e) => form.setData('village', e.target.value)} />
                                            </div>
                                            <div className="space-y-1">
                                                <Label>Kecamatan</Label>
                                                <Input value={form.data.district} onChange={(e) => form.setData('district', e.target.value)} required />
                                            </div>
                                            <div className="space-y-1">
                                                <Label>Kota/Kabupaten</Label>
                                                <Input value={form.data.city} onChange={(e) => form.setData('city', e.target.value)} required />
                                            </div>
                                            <div className="space-y-1">
                                                <Label>Provinsi</Label>
                                                <Input value={form.data.province} onChange={(e) => form.setData('province', e.target.value)} required />
                                            </div>
                                        </div>
                                        {form.errors && Object.keys(form.errors).length > 0 && (
                                            <ul className="text-sm text-destructive">
                                                {Object.values(form.errors).map((msg: unknown, i) => (
                                                    <li key={i}>{String(msg)}</li>
                                                ))}
                                            </ul>
                                        )}
                                        <div className="flex gap-2">
                                            <Button type="submit" disabled={form.processing}>Simpan Alamat</Button>
                                            {addresses.length > 0 && (
                                                <Button type="button" variant="outline" onClick={() => setShowForm(false)}>
                                                    Batal
                                                </Button>
                                            )}
                                        </div>
                                    </form>
                                </CardContent>
                            </Card>
                        )}

                        {addresses.length > 0 && selectedId && (
                            <Button
                                size="lg"
                                className="w-full"
                                onClick={() => router.get(`/checkout/confirm?address_id=${selectedId}`)}
                            >
                                Lanjut ke Konfirmasi
                            </Button>
                        )}
                    </div>

                    <Card className="h-fit">
                        <CardHeader>
                            <CardTitle className="text-base">Ringkasan</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {cart.items.map((it) => (
                                <div key={it.id} className="flex justify-between text-sm">
                                    <span className="line-clamp-1 flex-1">
                                        {it.product_name} {it.variant_name ? `(${it.variant_name})` : ''} ×{it.quantity}
                                    </span>
                                    <span className="ml-2 font-medium">{formatIDR(it.subtotal)}</span>
                                </div>
                            ))}
                            <div className="flex justify-between border-t pt-3 text-sm">
                                <span>Subtotal</span>
                                <span>{formatIDR(cart.subtotal)}</span>
                            </div>
                            <div className="flex justify-between text-sm">
                                <span>Ongkir</span>
                                <span>{formatIDR(cart.shipping_cost)}</span>
                            </div>
                            <div className="flex justify-between font-semibold">
                                <span>Total</span>
                                <span>{formatIDR(cart.subtotal + cart.shipping_cost)}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </StorefrontLayout>
    );
}
