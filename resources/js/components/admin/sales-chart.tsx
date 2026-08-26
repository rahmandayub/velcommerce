import {
    Bar,
    BarChart,
    CartesianGrid,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import { formatIDR } from '@/lib/format';

type ChartPoint = {
    label: string;
    revenue: number;
    orders: number;
};

export function SalesChart({ data }: { data: ChartPoint[] }) {
    return (
        <ResponsiveContainer width="100%" height={300}>
            <BarChart
                data={data}
                margin={{ top: 8, right: 8, left: 8, bottom: 0 }}
            >
                <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                <XAxis
                    dataKey="label"
                    tick={{ fontSize: 12 }}
                    className="text-muted-foreground"
                    interval="preserveStartEnd"
                />
                <YAxis
                    tick={{ fontSize: 12 }}
                    className="text-muted-foreground"
                    tickFormatter={(v: number) =>
                        `Rp ${(v / 1000).toFixed(0)}k`
                    }
                    width={64}
                />
                <Tooltip
                    formatter={(value: any, name: any) =>
                        name === 'revenue'
                            ? [formatIDR(Number(value)), 'Pendapatan']
                            : [value, 'Pesanan']
                    }
                    contentStyle={{
                        borderRadius: 8,
                        border: '1px solid hsl(var(--border))',
                        fontSize: 12,
                    }}
                />
                <Bar
                    dataKey="revenue"
                    fill="hsl(var(--primary))"
                    radius={[4, 4, 0, 0]}
                />
            </BarChart>
        </ResponsiveContainer>
    );
}
