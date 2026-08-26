import { Badge } from '@/components/ui/badge';

type Props = {
    status: string;
};

const labels: Record<string, string> = {
    pending: 'Pending',
    paid: 'Paid',
    shipped: 'Shipped',
    completed: 'Selesai',
    cancelled: 'Dibatalkan',
    failed: 'Gagal',
    unpaid: 'Belum Bayar',
    refunded: 'Refund',
};

const variants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    pending: 'secondary',
    paid: 'default',
    shipped: 'default',
    completed: 'default',
    cancelled: 'destructive',
    failed: 'destructive',
    unpaid: 'secondary',
};

export function OrderStatusBadge({ status }: Props) {
    const normalized = status.toLowerCase();

    return (
        <Badge variant={variants[normalized] ?? 'outline'}>
            {labels[normalized] ?? status}
        </Badge>
    );
}
