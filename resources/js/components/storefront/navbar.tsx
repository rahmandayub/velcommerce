import { Link, usePage } from '@inertiajs/react';
import { Menu, Search, ShoppingCart } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { UserMenuContent } from '@/components/user-menu-content';
import { useInitials } from '@/hooks/use-initials';
import { dashboard, login, register } from '@/routes';
import { home } from '@/routes';
import type { User } from '@/types';

type NavItem = {
    title: string;
    href: string;
};

const navItems: NavItem[] = [
    { title: 'Beranda', href: home().url },
    { title: 'Katalog', href: '#' },
    { title: 'Tentang', href: '#' },
];

export function Navbar() {
    const page = usePage();
    const props = page.props as unknown as {
        auth: { user: User | null };
        cartCount: number;
    };
    const { auth } = props;
    const cartCount = (props.cartCount as number) ?? 0;
    const getInitials = useInitials();

    return (
        <header className="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div className="mx-auto flex h-16 max-w-7xl items-center px-4">
                {/* Mobile Menu */}
                <div className="lg:hidden">
                    <Sheet>
                        <SheetTrigger asChild>
                            <Button
                                variant="ghost"
                                size="icon"
                                className="mr-2 h-9 w-9"
                                aria-label="Toggle navigation menu"
                            >
                                <Menu className="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent
                            side="left"
                            className="w-72 bg-background"
                        >
                            <SheetTitle className="sr-only">
                                Navigation menu
                            </SheetTitle>
                            <SheetHeader className="text-left">
                                <Link
                                    href={home()}
                                    className="flex items-center gap-2"
                                >
                                    <AppLogo />
                                </Link>
                            </SheetHeader>
                            <nav className="mt-6 flex flex-col gap-2">
                                {navItems.map((item) => (
                                    <Link
                                        key={item.title}
                                        href={item.href}
                                        className="rounded-md px-3 py-2 text-sm font-medium hover:bg-accent"
                                    >
                                        {item.title}
                                    </Link>
                                ))}
                                {auth.user ? (
                                    <Link
                                        href={dashboard()}
                                        className="rounded-md px-3 py-2 text-sm font-medium hover:bg-accent"
                                    >
                                        Dashboard
                                    </Link>
                                ) : null}
                            </nav>
                            <div className="mt-6">
                                <div className="relative">
                                    <Search className="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        placeholder="Cari produk..."
                                        className="pl-8"
                                    />
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <Link
                    href={home()}
                    className="flex items-center gap-2"
                    prefetch
                >
                    <AppLogo />
                </Link>

                {/* Desktop Navigation */}
                <nav className="ml-8 hidden items-center gap-6 lg:flex">
                    {navItems.map((item) => (
                        <Link
                            key={item.title}
                            href={item.href}
                            className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {item.title}
                        </Link>
                    ))}
                </nav>

                <div className="ml-auto flex items-center gap-2">
                    {/* Search - desktop */}
                    <div className="relative hidden md:block">
                        <Search className="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground" />
                        <Input
                            placeholder="Cari produk..."
                            className="w-[200px] pl-8 lg:w-[280px]"
                        />
                    </div>
                    <Button
                        variant="ghost"
                        size="icon"
                        className="md:hidden"
                        aria-label="Search"
                    >
                        <Search className="h-5 w-5" />
                    </Button>

                    {/* Cart */}
                    <Button
                        variant="ghost"
                        size="icon"
                        className="relative"
                        aria-label="Shopping cart"
                        asChild
                    >
                        <Link href="#">
                            <ShoppingCart className="h-5 w-5" />
                            {cartCount > 0 && (
                                <Badge className="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[10px]">
                                    {cartCount}
                                </Badge>
                            )}
                        </Link>
                    </Button>

                    {/* Auth */}
                    {auth.user ? (
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button
                                    variant="ghost"
                                    className="size-10 rounded-full p-1"
                                >
                                    <Avatar className="size-8 overflow-hidden rounded-full">
                                        <AvatarImage
                                            src={auth.user.avatar}
                                            alt={auth.user.name}
                                        />
                                        <AvatarFallback className="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                            {getInitials(auth.user.name ?? '')}
                                        </AvatarFallback>
                                    </Avatar>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent
                                className="w-56"
                                align="end"
                            >
                                <UserMenuContent user={auth.user} />
                            </DropdownMenuContent>
                        </DropdownMenu>
                    ) : (
                        <div className="hidden items-center gap-2 sm:flex">
                            <Button variant="ghost" asChild>
                                <Link href={login()}>Masuk</Link>
                            </Button>
                            <Button asChild>
                                <Link href={register()}>Daftar</Link>
                            </Button>
                        </div>
                    )}
                    {!auth.user && (
                        <div className="sm:hidden">
                            <Button size="sm" asChild>
                                <Link href={login()}>Masuk</Link>
                            </Button>
                        </div>
                    )}
                </div>
            </div>
        </header>
    );
}
