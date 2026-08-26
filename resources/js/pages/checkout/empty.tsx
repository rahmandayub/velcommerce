import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import StorefrontLayout from '@/layouts/storefront-layout';

export default function CheckoutEmpty() {
    return (
        <StorefrontLayout>
            <Head title="Checkout" />
            <div className="mx-auto max-w-lg px-4 py-16 text-center">
                <Card className="p-12">
                    <h1 className="text-xl font-semibold">Keranjang Kosong</h1>
                    <p className="mt-2 text-sm text-muted-foreground">
                        Tambahkan produk ke keranjang sebelum checkout.
                    </p>
                    <Button asChild className="mt-6">
                        <Link href="/products">Jelajahi Katalog</Link>
                    </Button>
                </Card>
            </div>
        </StorefrontLayout>
    );
}
