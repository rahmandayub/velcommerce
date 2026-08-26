import { Star } from 'lucide-react';

type StarRatingProps = {
    value: number;
    /** Render as an input (clickable) instead of read-only display. */
    editable?: boolean;
    onChange?: (value: number) => void;
    size?: number;
    className?: string;
};

export function StarRating({
    value,
    editable = false,
    onChange,
    size = 18,
    className,
}: StarRatingProps) {
    const stars = [1, 2, 3, 4, 5];

    return (
        <div
            className={`flex items-center gap-0.5 ${className ?? ''}`}
            role={editable ? 'radiogroup' : 'img'}
            aria-label={`Rating ${value} dari 5`}
        >
            {stars.map((star) => {
                const filled = star <= Math.round(value);

                const starEl = (
                    <Star
                        width={size}
                        height={size}
                        className={
                            filled
                                ? 'fill-yellow-400 text-yellow-400'
                                : 'fill-transparent text-muted-foreground'
                        }
                    />
                );

                if (!editable) {
                    return <span key={star}>{starEl}</span>;
                }

                return (
                    <button
                        key={star}
                        type="button"
                        aria-label={`Beri ${star} bintang`}
                        onClick={() => onChange?.(star)}
                        className="transition hover:scale-110"
                    >
                        {starEl}
                    </button>
                );
            })}
        </div>
    );
}
