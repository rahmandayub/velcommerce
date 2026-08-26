import { useState } from 'react';
import { cn } from '@/lib/utils';

type Image = {
    id: number;
    url: string;
    alt?: string | null;
};

type Props = {
    images: Image[];
    productName: string;
};

export function ProductGallery({ images, productName }: Props) {
    const [active, setActive] = useState(0);

    if (!images.length) {
        return (
            <div className="flex aspect-square items-center justify-center rounded-xl border bg-muted text-muted-foreground">
                No image
            </div>
        );
    }

    const main = images[active] ?? images[0];

    return (
        <div className="space-y-3">
            <div className="aspect-square overflow-hidden rounded-xl border bg-muted">
                <img
                    src={main.url}
                    alt={main.alt ?? productName}
                    className="h-full w-full object-cover"
                />
            </div>
            {images.length > 1 && (
                <div className="flex gap-2 overflow-auto pb-1">
                    {images.map((img, idx) => (
                        <button
                            key={img.id}
                            type="button"
                            onClick={() => setActive(idx)}
                            className={cn(
                                'h-20 w-20 shrink-0 overflow-hidden rounded-lg border-2 bg-muted',
                                idx === active
                                    ? 'border-primary'
                                    : 'border-transparent hover:border-muted-foreground/30',
                            )}
                        >
                            <img
                                src={img.url}
                                alt={img.alt ?? `${productName} ${idx + 1}`}
                                className="h-full w-full object-cover"
                            />
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}
