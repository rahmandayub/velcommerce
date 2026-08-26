import { Link } from '@inertiajs/react';
import AppLogo from '@/components/app-logo';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { home } from '@/routes';

export function Footer() {
    return (
        <footer className="border-t bg-muted/20">
            <div className="mx-auto max-w-7xl px-4 py-12">
                <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    {/* Brand */}
                    <div className="space-y-3">
                        <Link
                            href={home()}
                            className="flex items-center gap-2"
                        >
                            <AppLogo />
                        </Link>
                        <p className="text-sm leading-relaxed text-muted-foreground">
                            Velcommerce — platform e-commerce modern untuk
                            kebutuhan fashion, elektronik, dan gaya hidup Anda.
                            Belanja aman, cepat, terpercaya.
                        </p>
                    </div>

                    {/* Kategori */}
                    <div>
                        <h3 className="mb-3 text-sm font-semibold">Kategori</h3>
                        <ul className="space-y-2 text-sm text-muted-foreground">
                            <li>
                                <Link
                                    href="#"
                                    className="hover:text-foreground"
                                >
                                    Pakaian Pria
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="hover:text-foreground"
                                >
                                    Pakaian Wanita
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="hover:text-foreground"
                                >
                                    Elektronik
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="hover:text-foreground"
                                >
                                    Aksesoris
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="hover:text-foreground"
                                >
                                    Sepatu & Tas
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* Bantuan */}
                    <div>
                        <h3 className="mb-3 text-sm font-semibold">Bantuan</h3>
                        <ul className="space-y-2 text-sm text-muted-foreground">
                            <li>
                                <Link
                                    href="#"
                                    className="hover:text-foreground"
                                >
                                    Pusat Bantuan
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="hover:text-foreground"
                                >
                                    Cara Berbelanja
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="hover:text-foreground"
                                >
                                    Pengembalian
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="hover:text-foreground"
                                >
                                    Syarat & Ketentuan
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="hover:text-foreground"
                                >
                                    Kebijakan Privasi
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* Newsletter */}
                    <div>
                        <h3 className="mb-3 text-sm font-semibold">
                            Newsletter
                        </h3>
                        <p className="mb-3 text-sm text-muted-foreground">
                            Dapatkan promo dan update terbaru langsung ke email
                            Anda.
                        </p>
                        <form
                            className="flex gap-2"
                            onSubmit={(e) => e.preventDefault()}
                        >
                            <Input
                                placeholder="Email Anda"
                                type="email"
                                className="flex-1"
                            />
                            <Button type="submit">Langganan</Button>
                        </form>
                    </div>
                </div>

                <div className="mt-10 flex flex-col items-center justify-between gap-4 border-t pt-6 text-sm text-muted-foreground md:flex-row">
                    <p>
                        © {new Date().getFullYear()} Velcommerce. Hak cipta
                        dilindungi.
                    </p>
                    <p className="text-xs">
                        Dibuat dengan ♥ untuk Indonesia
                    </p>
                </div>
            </div>
        </footer>
    );
}
