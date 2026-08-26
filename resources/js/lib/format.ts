/**
 * Format a numeric value as Indonesian Rupiah.
 */
export function formatIDR(value: number | string | null | undefined): string {
    const n = typeof value === 'string' ? parseFloat(value) : (value ?? 0);

    if (Number.isNaN(n)) {
        return 'Rp 0';
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(n);
}

export function formatPrice(value: number | string | null | undefined): string {
    return formatIDR(value);
}
