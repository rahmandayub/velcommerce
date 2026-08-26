import { Head, Link } from '@inertiajs/react';
import { Pagination } from '@/components/storefront/pagination';
import { ReviewCard } from '@/components/storefront/review-card';
import { Button } from '@/components/ui/button';
import StorefrontLayout from '@/layouts/storefront-layout';

type Review = {
    id: number;
    rating: number;
    title: string | null;
    body: string | null;
    created_at: string;
    user: { name: string | null } | null;
};

type PaginatedReview = {
    data: Review[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    product: { id: number; slug: string; name: string };
    reviews: PaginatedReview;
};

export default function ProductReviews({ product, reviews }: Props) {
    return (
        <StorefrontLayout>
            <Head title={`Ulasan — ${product.name}`} />
            <div className="mx-auto max-w-3xl px-4 py-8">
                <Link
                    href={`/products/${product.slug}`}
                    className="text-sm text-muted-foreground hover:underline"
                >
                    ← Kembali ke {product.name}
                </Link>
                <h1 className="mt-2 text-2xl font-bold">
                    Ulasan {product.name}
                </h1>

                <div className="mt-6 space-y-2">
                    {reviews.data.length === 0 ? (
                        <p className="text-sm text-muted-foreground">
                            Belum ada ulasan untuk produk ini.
                        </p>
                    ) : (
                        reviews.data.map((review) => (
                            <ReviewCard key={review.id} review={review} />
                        ))
                    )}
                </div>

                {reviews.links.length > 3 && (
                    <div className="mt-6">
                        <Pagination links={reviews.links} />
                    </div>
                )}

                <div className="mt-6">
                    <Button asChild>
                        <Link href={`/products/${product.slug}`}>
                            Lihat Produk
                        </Link>
                    </Button>
                </div>
            </div>
        </StorefrontLayout>
    );
}
