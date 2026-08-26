import { router } from '@inertiajs/react';
import { Heart } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';
import { store as wishlistStore } from '@/routes/wishlist';

type WishlistButtonProps = {
    productId: number;
    isWishlisted: boolean;
    className?: string;
};

export function WishlistButton({
    productId,
    isWishlisted,
    className,
}: WishlistButtonProps) {
    const [active, setActive] = useState(isWishlisted);
    const [processing, setProcessing] = useState(false);

    function toggle() {
        if (processing) {
            return;
        }

        setProcessing(true);

        router.post(
            wishlistStore().url,
            { product_id: productId },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setActive((prev) => !prev);
                    toast.success(
                        active ? 'Dihapus dari wishlist.' : 'Ditambahkan ke wishlist.',
                    );
                },
                onError: () => {
                    toast.error('Gagal memperbarui wishlist.');
                },
                onFinish: () => setProcessing(false),
            },
        );
    }

    return (
        <button
            type="button"
            onClick={toggle}
            disabled={processing}
            aria-label={active ? 'Hapus dari wishlist' : 'Tambah ke wishlist'}
            className={`inline-flex items-center justify-center rounded-full p-2 transition hover:bg-accent ${
                className ?? ''
            }`}
        >
            <Heart
                className={
                    active
                        ? 'fill-destructive text-destructive'
                        : 'text-muted-foreground'
                }
                width={20}
                height={20}
            />
        </button>
    );
}
