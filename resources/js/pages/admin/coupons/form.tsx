import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import {
    update as couponUpdate,
    store as couponStore,
} from '@/routes/admin/coupons';

type Coupon = {
    id: number;
    code: string;
    type: 'percent' | 'fixed';
    value: number;
    min_order_amount: number;
    max_discount_amount: number | null;
    usage_limit: number | null;
    per_user_limit: number | null;
    is_active: boolean;
    starts_at: string | null;
    expires_at: string | null;
};

type Props = {
    coupon: Coupon | null;
};

function toDateInput(value: string | null): string {
    if (!value) {
        return '';
    }

    return new Date(value).toISOString().slice(0, 16);
}

export default function CouponForm({ coupon }: Props) {
    const isEdit = coupon !== null;
    const [type, setType] = useState<'percent' | 'fixed'>(
        coupon?.type ?? 'percent',
    );

    const form = {
        data: {
            code: coupon?.code ?? '',
            type: coupon?.type ?? 'percent',
            value: coupon?.value ?? '',
            min_order_amount: coupon?.min_order_amount ?? '',
            max_discount_amount: coupon?.max_discount_amount ?? '',
            usage_limit: coupon?.usage_limit ?? '',
            per_user_limit: coupon?.per_user_limit ?? '',
            starts_at: toDateInput(coupon?.starts_at ?? null),
            expires_at: toDateInput(coupon?.expires_at ?? null),
            is_active: coupon?.is_active ?? true,
        },
        errors: {} as Record<string, string>,
        processing: false,
        setData: (key: string, value: unknown) => {
            form.data = { ...form.data, [key]: value };
        },
        post: (url: string) => submit(url, 'post'),
        put: (url: string) => submit(url, 'put'),
    };

    function submit(url: string, method: 'post' | 'put') {
        const payload = {
            ...form.data,
            type,
            is_active: form.data.is_active,
        };

        if (method === 'post') {
            router.post(url, payload, { preserveScroll: true });
        } else {
            router.put(url, payload, { preserveScroll: true });
        }
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin' },
                { title: 'Coupons', href: '/admin/coupons' },
                { title: isEdit ? 'Edit' : 'Buat', href: '#' },
            ]}
        >
            <Head
                title={isEdit ? 'Admin — Edit Kupon' : 'Admin — Buat Kupon'}
            />
            <div className="mx-auto max-w-2xl space-y-4 p-4">
                <h1 className="text-2xl font-bold">
                    {isEdit ? 'Edit Kupon' : 'Buat Kupon'}
                </h1>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-base">
                            Detail Kupon
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="space-y-1">
                            <Label>Kode</Label>
                            <Input
                                value={form.data.code}
                                onChange={(e) =>
                                    form.setData(
                                        'code',
                                        e.target.value.toUpperCase(),
                                    )
                                }
                                placeholder="WELCOME10"
                            />
                        </div>

                        <div className="space-y-1">
                            <Label>Tipe Diskon</Label>
                            <div className="flex gap-2">
                                <Button
                                    type="button"
                                    variant={
                                        type === 'percent'
                                            ? 'default'
                                            : 'outline'
                                    }
                                    onClick={() => {
                                        setType('percent');
                                        form.setData('type', 'percent');
                                    }}
                                >
                                    Persen (%)
                                </Button>
                                <Button
                                    type="button"
                                    variant={
                                        type === 'fixed' ? 'default' : 'outline'
                                    }
                                    onClick={() => {
                                        setType('fixed');
                                        form.setData('type', 'fixed');
                                    }}
                                >
                                    Nominal (Rp)
                                </Button>
                            </div>
                        </div>

                        <div className="grid gap-3 sm:grid-cols-2">
                            <div className="space-y-1">
                                <Label>
                                    {type === 'percent'
                                        ? 'Persen (%)'
                                        : 'Nominal (Rp)'}
                                </Label>
                                <Input
                                    type="number"
                                    value={form.data.value}
                                    onChange={(e) =>
                                        form.setData('value', e.target.value)
                                    }
                                />
                            </div>
                            <div className="space-y-1">
                                <Label>Min. Order (Rp)</Label>
                                <Input
                                    type="number"
                                    value={form.data.min_order_amount}
                                    onChange={(e) =>
                                        form.setData(
                                            'min_order_amount',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>
                        </div>

                        <div className="grid gap-3 sm:grid-cols-2">
                            <div className="space-y-1">
                                <Label>
                                    Maks. Diskon (Rp){' '}
                                    {type === 'fixed' && (
                                        <span className="text-xs text-muted-foreground">
                                            — abaikan
                                        </span>
                                    )}
                                </Label>
                                <Input
                                    type="number"
                                    value={form.data.max_discount_amount}
                                    onChange={(e) =>
                                        form.setData(
                                            'max_discount_amount',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>
                            <div className="space-y-1">
                                <Label>Limit Penggunaan Global</Label>
                                <Input
                                    type="number"
                                    value={form.data.usage_limit}
                                    onChange={(e) =>
                                        form.setData(
                                            'usage_limit',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="tanpa batas"
                                />
                            </div>
                        </div>

                        <div className="grid gap-3 sm:grid-cols-2">
                            <div className="space-y-1">
                                <Label>Limit per User</Label>
                                <Input
                                    type="number"
                                    value={form.data.per_user_limit}
                                    onChange={(e) =>
                                        form.setData(
                                            'per_user_limit',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="1"
                                />
                            </div>
                            <div className="space-y-1">
                                <Label>Status</Label>
                                <select
                                    className="h-9 w-full rounded-md border bg-background px-3 text-sm"
                                    value={form.data.is_active ? '1' : '0'}
                                    onChange={(e) =>
                                        form.setData(
                                            'is_active',
                                            e.target.value === '1',
                                        )
                                    }
                                >
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div className="grid gap-3 sm:grid-cols-2">
                            <div className="space-y-1">
                                <Label>Mulai (opsional)</Label>
                                <Input
                                    type="datetime-local"
                                    value={form.data.starts_at}
                                    onChange={(e) =>
                                        form.setData(
                                            'starts_at',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>
                            <div className="space-y-1">
                                <Label>Berakhir (opsional)</Label>
                                <Input
                                    type="datetime-local"
                                    value={form.data.expires_at}
                                    onChange={(e) =>
                                        form.setData(
                                            'expires_at',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>
                        </div>

                        <div className="flex gap-2 pt-2">
                            <Button
                                type="button"
                                onClick={() =>
                                    isEdit
                                        ? form.put(
                                              couponUpdate({
                                                  coupon: coupon!.id,
                                              }).url,
                                          )
                                        : form.post(couponStore().url)
                                }
                            >
                                Simpan
                            </Button>
                            <Button variant="outline" asChild>
                                <Link href="/admin/coupons">Batal</Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
