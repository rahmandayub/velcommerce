import { Footer } from '@/components/storefront/footer';
import { Navbar } from '@/components/storefront/navbar';

type Props = {
    children: React.ReactNode;
};

export default function StorefrontLayout({ children }: Props) {
    return (
        <div className="flex min-h-screen flex-col bg-background">
            <Navbar />
            <main className="flex-1">{children}</main>
            <Footer />
        </div>
    );
}
