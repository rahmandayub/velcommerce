import { StarRating } from '@/components/storefront/star-rating';

type ReviewCardProps = {
    review: {
        id: number;
        rating: number;
        title: string | null;
        body: string | null;
        created_at: string;
        user: { name: string | null } | null;
    };
};

export function ReviewCard({ review }: ReviewCardProps) {
    return (
        <div className="border-b py-4 last:border-b-0">
            <div className="flex items-center justify-between gap-2">
                <div className="flex items-center gap-2">
                    <StarRating value={review.rating} size={16} />
                    <span className="text-sm font-medium">
                        {review.title ?? 'Ulasan'}
                    </span>
                </div>
                <span className="text-xs text-muted-foreground">
                    {new Date(review.created_at).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                    })}
                </span>
            </div>
            {review.body && (
                <p className="mt-2 text-sm text-muted-foreground">{review.body}</p>
            )}
            <p className="mt-2 text-xs text-muted-foreground">
                — {review.user?.name ?? 'Pengguna'}
            </p>
        </div>
    );
}
