import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

type LinkItem = { url: string | null; label: string; active: boolean };

type Props = {
    links: LinkItem[];
};

export function Pagination({ links }: Props) {
    if (!links?.length) {
        return null;
    }

    return (
        <div className="flex flex-wrap justify-center gap-1">
            {links.map((link, idx) => {
                const label = link.label.replace('&laquo;', '«').replace('&raquo;', '»');

                return link.url ? (
                    <Button
                        key={idx}
                        variant={link.active ? 'default' : 'outline'}
                        size="sm"
                        asChild
                    >
                        <Link href={link.url} dangerouslySetInnerHTML={{ __html: label }} />
                    </Button>
                ) : (
                    <Button key={idx} variant="outline" size="sm" disabled>
                        <span dangerouslySetInnerHTML={{ __html: label }} />
                    </Button>
                );
            })}
        </div>
    );
}
