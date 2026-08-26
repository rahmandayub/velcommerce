import { Head, router } from '@inertiajs/react';
import { Star, Trash2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';

type Review = {
    id: number;
    rating: number;
    title: string | null;
    body: string | null;
    is_approved: boolean;
    created_at: string;
    user: { name: string | null } | null;
    product: { id: number; name: string | null } | null;
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    reviews: Paginated<Review>;
};

export default function AdminReviewsIndex({ reviews }: Props) {
    function remove(id: number) {
        if (!confirm('Hapus ulasan ini?')) {
            return;
        }

        router.delete(`/admin/reviews/${id}`, { preserveScroll: true });
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin' },
                { title: 'Reviews', href: '/admin/reviews' },
            ]}
        >
            <Head title="Admin — Reviews" />
            <div className="space-y-4 p-4">
                <h1 className="text-2xl font-bold">Moderasi Ulasan</h1>

                <Card>
                    <CardContent className="divide-y p-0">
                        {reviews.data.length === 0 ? (
                            <p className="p-6 text-center text-muted-foreground">
                                Belum ada ulasan.
                            </p>
                        ) : (
                            reviews.data.map((r) => (
                                <div
                                    key={r.id}
                                    className="flex items-start justify-between gap-4 p-4"
                                >
                                    <div className="flex-1">
                                        <div className="flex items-center gap-2">
                                            <div className="flex text-yellow-500">
                                                {Array.from({ length: 5 }).map(
                                                    (_, i) => (
                                                        <Star
                                                            key={i}
                                                            className={`h-4 w-4 ${
                                                                i < r.rating
                                                                    ? 'fill-yellow-400'
                                                                    : 'text-muted-foreground'
                                                            }`}
                                                        />
                                                    ),
                                                )}
                                            </div>
                                            <span className="text-sm font-medium">
                                                {r.title ?? 'Ulasan'}
                                            </span>
                                            <Badge
                                                variant={
                                                    r.is_approved
                                                        ? 'default'
                                                        : 'secondary'
                                                }
                                            >
                                                {r.is_approved
                                                    ? 'Disetujui'
                                                    : 'Menunggu'}
                                            </Badge>
                                        </div>
                                        {r.body && (
                                            <p className="mt-1 text-sm text-muted-foreground">
                                                {r.body}
                                            </p>
                                        )}
                                        <p className="mt-1 text-xs text-muted-foreground">
                                            {r.user?.name ?? 'Pengguna'} —{' '}
                                            {r.product?.name ?? 'Produk'} ·{' '}
                                            {new Date(
                                                r.created_at,
                                            ).toLocaleDateString('id-ID')}
                                        </p>
                                    </div>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        onClick={() => remove(r.id)}
                                    >
                                        <Trash2 className="h-4 w-4 text-destructive" />
                                    </Button>
                                </div>
                            ))
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
