import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2, Power } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { formatIDR } from '@/lib/format';
import {
    create as couponCreate,
    destroy as couponDestroy,
    edit as couponEdit,
    toggle as couponToggle,
} from '@/routes/admin/coupons';

type Coupon = {
    id: number;
    code: string;
    type: 'percent' | 'fixed';
    value: number;
    min_order_amount: number;
    max_discount_amount: number | null;
    usage_limit: number | null;
    usage_count: number;
    per_user_limit: number | null;
    is_active: boolean;
    starts_at: string | null;
    expires_at: string | null;
    created_at: string;
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    coupons: Paginated<Coupon>;
};

function formatValue(type: string, value: number): string {
    return type === 'percent' ? `${value}%` : formatIDR(value);
}

export default function AdminCouponsIndex({ coupons }: Props) {
    function remove(id: number) {
        if (!confirm('Hapus kupon ini?')) {
            return;
        }

        router.delete(couponDestroy({ coupon: id }).url, {
            preserveScroll: true,
        });
    }

    function toggle(id: number) {
        router.post(
            couponToggle({ coupon: id }).url,
            {},
            { preserveScroll: true },
        );
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin' },
                { title: 'Coupons', href: '/admin/coupons' },
            ]}
        >
            <Head title="Admin — Coupons" />
            <div className="space-y-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold">Kupon / Voucher</h1>
                    <Button asChild>
                        <Link href={couponCreate().url}>
                            <Plus className="h-4 w-4" /> Buat Kupon
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardContent className="p-0">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="border-b bg-muted/40 text-left">
                                    <tr>
                                        <th className="px-4 py-3 font-medium">
                                            Kode
                                        </th>
                                        <th className="px-4 py-3 font-medium">
                                            Tipe
                                        </th>
                                        <th className="px-4 py-3 font-medium">
                                            Nilai
                                        </th>
                                        <th className="px-4 py-3 font-medium">
                                            Min. Order
                                        </th>
                                        <th className="px-4 py-3 font-medium">
                                            Penggunaan
                                        </th>
                                        <th className="px-4 py-3 font-medium">
                                            Berlaku
                                        </th>
                                        <th className="px-4 py-3 font-medium">
                                            Status
                                        </th>
                                        <th className="px-4 py-3 text-right font-medium">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {coupons.data.map((c) => (
                                        <tr
                                            key={c.id}
                                            className="border-b last:border-0"
                                        >
                                            <td className="px-4 py-3 font-medium">
                                                {c.code}
                                            </td>
                                            <td className="px-4 py-3">
                                                {c.type === 'percent'
                                                    ? 'Persen'
                                                    : 'Nominal'}
                                            </td>
                                            <td className="px-4 py-3">
                                                {formatValue(c.type, c.value)}
                                                {c.max_discount_amount !==
                                                    null &&
                                                    c.type === 'percent' && (
                                                        <span className="text-xs text-muted-foreground">
                                                            {' '}
                                                            (maks{' '}
                                                            {formatIDR(
                                                                c.max_discount_amount,
                                                            )}
                                                            )
                                                        </span>
                                                    )}
                                            </td>
                                            <td className="px-4 py-3">
                                                {c.min_order_amount > 0
                                                    ? formatIDR(
                                                          c.min_order_amount,
                                                      )
                                                    : '—'}
                                            </td>
                                            <td className="px-4 py-3">
                                                {c.usage_count}
                                                {c.usage_limit
                                                    ? ` / ${c.usage_limit}`
                                                    : ''}
                                            </td>
                                            <td className="px-4 py-3 text-xs text-muted-foreground">
                                                {c.starts_at
                                                    ? new Date(
                                                          c.starts_at,
                                                      ).toLocaleDateString(
                                                          'id-ID',
                                                      )
                                                    : '—'}{' '}
                                                s/d{' '}
                                                {c.expires_at
                                                    ? new Date(
                                                          c.expires_at,
                                                      ).toLocaleDateString(
                                                          'id-ID',
                                                      )
                                                    : 'selamanya'}
                                            </td>
                                            <td className="px-4 py-3">
                                                <Badge
                                                    variant={
                                                        c.is_active
                                                            ? 'default'
                                                            : 'secondary'
                                                    }
                                                >
                                                    {c.is_active
                                                        ? 'Aktif'
                                                        : 'Nonaktif'}
                                                </Badge>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-end gap-1">
                                                    <Button
                                                        size="icon"
                                                        variant="ghost"
                                                        asChild
                                                    >
                                                        <Link
                                                            href={
                                                                couponEdit({
                                                                    coupon: c.id,
                                                                }).url
                                                            }
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                    <Button
                                                        size="icon"
                                                        variant="ghost"
                                                        onClick={() =>
                                                            toggle(c.id)
                                                        }
                                                        title="Aktif/Nonaktif"
                                                    >
                                                        <Power className="h-4 w-4" />
                                                    </Button>
                                                    <Button
                                                        size="icon"
                                                        variant="ghost"
                                                        onClick={() =>
                                                            remove(c.id)
                                                        }
                                                    >
                                                        <Trash2 className="h-4 w-4 text-destructive" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                    {coupons.data.length === 0 && (
                                        <tr>
                                            <td
                                                colSpan={8}
                                                className="px-4 py-8 text-center text-muted-foreground"
                                            >
                                                Belum ada kupon.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
