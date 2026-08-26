import { router, useForm } from '@inertiajs/react';
import { Check, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';
import { formatIDR } from '@/lib/format';
import { apply as couponApply, remove as couponRemove } from '@/routes/coupons';

type AppliedCoupon = {
    code: string;
    type: 'percent' | 'fixed';
    value: number;
    discount: number;
};

type CouponInputProps = {
    coupon: AppliedCoupon | null;
    compact?: boolean;
};

export function CouponInput({ coupon, compact = false }: CouponInputProps) {
    const [open, setOpen] = useState(!coupon);
    const form = useForm({ coupon_code: '' });

    function apply(e: React.FormEvent) {
        e.preventDefault();
        form.post(couponApply().url, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('coupon_code');
                setOpen(false);
            },
            onError: (errors) => {
                const message = errors.coupon_code ?? 'Kupon tidak valid.';
                toast.error(message);
            },
        });
    }

    function remove() {
        router.delete(couponRemove().url, {
            preserveScroll: true,
            onSuccess: () => setOpen(true),
        });
    }

    if (coupon) {
        return (
            <div className="flex items-center justify-between gap-2 rounded-md border border-primary/40 bg-primary/5 px-3 py-2">
                <div className="flex items-center gap-2 text-sm">
                    <Check className="h-4 w-4 text-primary" />
                    <span className="font-medium">{coupon.code}</span>
                    <span className="text-muted-foreground">
                        -{formatIDR(coupon.discount)}
                    </span>
                </div>
                <button
                    type="button"
                    onClick={remove}
                    aria-label="Hapus kupon"
                    className="text-muted-foreground transition hover:text-destructive"
                >
                    <Trash2 className="h-4 w-4" />
                </button>
            </div>
        );
    }

    if (!open && !compact) {
        return (
            <button
                type="button"
                onClick={() => setOpen(true)}
                className="text-sm font-medium text-primary hover:underline"
            >
                Punya kode voucher?
            </button>
        );
    }

    return (
        <form onSubmit={apply} className="flex items-center gap-2">
            <input
                value={form.data.coupon_code}
                onChange={(e) =>
                    form.setData('coupon_code', e.target.value.toUpperCase())
                }
                placeholder="Kode voucher"
                className="h-9 flex-1 rounded-md border bg-background px-3 text-sm"
            />
            <button
                type="submit"
                disabled={form.processing || !form.data.coupon_code}
                className="h-9 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground disabled:opacity-60"
            >
                Terapkan
            </button>
        </form>
    );
}
