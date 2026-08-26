import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

type Variant = {
    id: number;
    sku: string;
    name: string | null;
    effective_price: number;
    stock: number;
    attributes: Record<string, string> | null;
};

type Props = {
    variants: Variant[];
    selectedId: number | null;
    onSelect: (id: number) => void;
};

export function VariantSelector({ variants, selectedId, onSelect }: Props) {
    if (!variants.length) {
        return null;
    }

    // Group by color/size display: just list chips.
    return (
        <div className="space-y-3">
            <p className="text-sm font-medium">Varian</p>
            <div className="flex flex-wrap gap-2">
                {variants.map((v) => {
                    const disabled = v.stock <= 0;
                    const selected = v.id === selectedId;

                    return (
                        <Button
                            key={v.id}
                            type="button"
                            variant={selected ? 'default' : 'outline'}
                            size="sm"
                            disabled={disabled}
                            onClick={() => onSelect(v.id)}
                            className={cn(disabled && 'opacity-50')}
                        >
                            {v.name ?? v.sku}
                            {disabled ? ' (Habis)' : ` — Stok ${v.stock}`}
                        </Button>
                    );
                })}
            </div>
        </div>
    );
}
