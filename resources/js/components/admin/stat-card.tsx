import { Card, CardContent } from '@/components/ui/card';

type StatCardProps = {
    title: string;
    value: string;
    hint?: string;
    icon?: React.ReactNode;
};

export function StatCard({ title, value, hint, icon }: StatCardProps) {
    return (
        <Card>
            <CardContent className="flex items-center justify-between gap-3 p-4">
                <div>
                    <p className="text-sm text-muted-foreground">{title}</p>
                    <p className="mt-1 text-2xl font-bold">{value}</p>
                    {hint && (
                        <p className="text-xs text-muted-foreground">{hint}</p>
                    )}
                </div>
                {icon && <div className="text-muted-foreground">{icon}</div>}
            </CardContent>
        </Card>
    );
}
