import { Link } from '@inertiajs/react';
import { ArrowRight, ShieldCheck, Truck, Undo2 } from 'lucide-react';
import { SeoHead } from '@/components/seo-head';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import StorefrontLayout from '@/layouts/storefront-layout';
import { dashboard, login, register } from '@/routes';
import { index as productsIndex } from '@/routes/products';

export default function Welcome() {
    return (
        <StorefrontLayout>
            <SeoHead
                title="Velcommerce - Belanja Online Terpercaya"
                description="Temukan ribuan produk pilihan — fashion, elektronik, hingga kebutuhan harian dengan harga terbaik dan pengiriman super cepat ke seluruh Indonesia."
                canonical={
                    typeof window !== 'undefined'
                        ? window.location.origin
                        : undefined
                }
                type="website"
            />

            {/* Hero */}
            <section className="bg-gradient-to-br from-primary/10 via-background to-secondary/10">
                <div className="mx-auto grid max-w-7xl gap-8 px-4 py-12 md:grid-cols-2 md:items-center md:py-20">
                    <div className="space-y-6">
                        <Badge variant="secondary" className="w-fit">
                            ✨ Koleksi Terbaru 2026
                        </Badge>
                        <h1 className="text-4xl leading-tight font-bold tracking-tight md:text-5xl">
                            Belanja Mudah,
                            <span className="text-primary"> Cepat & Aman</span>
                        </h1>
                        <p className="max-w-xl text-lg leading-relaxed text-muted-foreground">
                            Temukan ribuan produk pilihan — fashion, elektronik,
                            hingga kebutuhan harian — dengan harga terbaik dan
                            pengiriman super cepat ke seluruh Indonesia.
                        </p>
                        <div className="flex flex-wrap gap-3">
                            <Button size="lg" asChild>
                                <Link href={productsIndex()}>
                                    Jelajahi Katalog{' '}
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </Link>
                            </Button>
                            <Button size="lg" variant="outline" asChild>
                                <Link href={dashboard()}>Dashboard</Link>
                            </Button>
                        </div>
                        <div className="flex flex-wrap items-center gap-6 pt-4 text-sm text-muted-foreground">
                            <span className="flex items-center gap-2">
                                <Truck className="h-4 w-4" /> Gratis Ongkir *S&K
                            </span>
                            <span className="flex items-center gap-2">
                                <ShieldCheck className="h-4 w-4" /> Pembayaran
                                Aman
                            </span>
                            <span className="flex items-center gap-2">
                                <Undo2 className="h-4 w-4" /> Retur 14 Hari
                            </span>
                        </div>
                    </div>
                    <div className="relative">
                        <div className="aspect-[4/3] overflow-hidden rounded-2xl border bg-muted shadow-xl">
                            <img
                                src="https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=800&auto=format&fit=crop"
                                alt="Hero fashion"
                                className="h-full w-full object-cover"
                                loading="eager"
                                fetchPriority="high"
                                decoding="async"
                                width={800}
                                height={600}
                            />
                        </div>
                        <Card className="absolute -bottom-6 -left-4 hidden w-64 shadow-lg md:block">
                            <CardContent className="p-4">
                                <p className="text-sm font-medium">
                                    Flash Sale Hari Ini
                                </p>
                                <p className="text-xs text-muted-foreground">
                                    Diskon hingga 50% untuk produk pilihan
                                </p>
                                <div className="mt-3 flex gap-2">
                                    <Badge variant="destructive">
                                        02 : 14 : 33
                                    </Badge>
                                    <Button size="sm" className="ml-auto">
                                        Lihat
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </section>

            {/* Categories */}
            <section className="mx-auto max-w-7xl px-4 py-12">
                <div className="mb-6 flex items-center justify-between">
                    <h2 className="text-2xl font-semibold tracking-tight">
                        Kategori Populer
                    </h2>
                    <Button variant="ghost" asChild>
                        <Link href="#">Lihat Semua</Link>
                    </Button>
                </div>
                <div className="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6">
                    {[
                        'Pakaian Pria',
                        'Pakaian Wanita',
                        'Elektronik',
                        'Sepatu',
                        'Tas',
                        'Aksesoris',
                    ].map((cat) => (
                        <Link
                            key={cat}
                            href="#"
                            className="group rounded-xl border bg-card p-6 text-center transition hover:border-primary/50 hover:shadow-md"
                        >
                            <div className="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary group-hover:bg-primary group-hover:text-primary-foreground">
                                <span className="text-sm font-bold">
                                    {cat.charAt(0)}
                                </span>
                            </div>
                            <p className="text-sm font-medium">{cat}</p>
                        </Link>
                    ))}
                </div>
            </section>

            {/* Featured Products Placeholder */}
            <section className="bg-muted/30">
                <div className="mx-auto max-w-7xl px-4 py-12">
                    <div className="mb-6 flex items-center justify-between">
                        <h2 className="text-2xl font-semibold tracking-tight">
                            Produk Unggulan
                        </h2>
                        <Button variant="ghost" asChild>
                            <Link href="#">Lihat Semua</Link>
                        </Button>
                    </div>
                    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {[1, 2, 3, 4].map((i) => (
                            <Card
                                key={i}
                                className="overflow-hidden transition hover:shadow-lg"
                            >
                                <div className="aspect-square bg-muted">
                                    <img
                                        src={`https://picsum.photos/seed/vel${i}/400/400`}
                                        alt={`Produk ${i}`}
                                        className="h-full w-full object-cover"
                                        loading="lazy"
                                        decoding="async"
                                        width={400}
                                        height={400}
                                    />
                                </div>
                                <CardContent className="space-y-2 p-4">
                                    <h3 className="line-clamp-1 text-sm font-medium">
                                        Produk Contoh {i} — Kualitas Premium
                                    </h3>
                                    <div className="flex items-center gap-2">
                                        <span className="font-semibold">
                                            Rp{' '}
                                            {(
                                                199000 +
                                                i * 50000
                                            ).toLocaleString('id-ID')}
                                        </span>
                                        <span className="text-xs text-muted-foreground line-through">
                                            Rp{' '}
                                            {(
                                                299000 +
                                                i * 50000
                                            ).toLocaleString('id-ID')}
                                        </span>
                                    </div>
                                    <Badge variant="secondary" className="mt-1">
                                        Stok Tersedia
                                    </Badge>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                    <div className="mt-8 text-center">
                        <p className="mb-3 text-sm text-muted-foreground">
                            Produk akan diambil dari database di Fase 2 —
                            katalog dinamis siap.
                        </p>
                        <div className="flex justify-center gap-2">
                            <Button variant="outline" asChild>
                                <Link href={login()}>Masuk untuk Belanja</Link>
                            </Button>
                            <Button asChild>
                                <Link href={register()}>Daftar Sekarang</Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </section>
        </StorefrontLayout>
    );
}
