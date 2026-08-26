import { Head } from '@inertiajs/react';

type SeoProps = {
    title?: string | null;
    description?: string | null;
    canonical?: string | null;
    image?: string | null;
    type?: string | null;
    jsonLd?: Record<string, unknown> | null;
    breadcrumbLd?: Record<string, unknown> | null;
};

export function SeoHead({
    title,
    description,
    canonical,
    image,
    type,
    jsonLd,
    breadcrumbLd,
}: SeoProps) {
    return (
        <Head>
            {title && <title>{title}</title>}
            {description && <meta name="description" content={description} />}
            {canonical && <link rel="canonical" href={canonical} />}

            {/* Open Graph */}
            {title && <meta property="og:title" content={title} />}
            {description && (
                <meta property="og:description" content={description} />
            )}
            {canonical && <meta property="og:url" content={canonical} />}
            {type && <meta property="og:type" content={type} />}
            {image && <meta property="og:image" content={image} />}

            {/* Twitter */}
            <meta name="twitter:card" content="summary_large_image" />
            {title && <meta name="twitter:title" content={title} />}
            {description && (
                <meta name="twitter:description" content={description} />
            )}
            {image && <meta name="twitter:image" content={image} />}

            {/* JSON-LD */}
            {jsonLd && (
                <script type="application/ld+json">
                    {JSON.stringify(jsonLd)}
                </script>
            )}
            {breadcrumbLd && (
                <script type="application/ld+json">
                    {JSON.stringify(breadcrumbLd)}
                </script>
            )}
        </Head>
    );
}
