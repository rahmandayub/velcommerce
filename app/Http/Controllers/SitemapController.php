<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', 3600, function (): string {
            $products = Product::query()
                ->where('is_active', true)
                ->select(['slug', 'updated_at'])
                ->latest('updated_at')
                ->get();

            $categories = Category::query()
                ->where('is_active', true)
                ->select(['slug', 'updated_at'])
                ->latest('updated_at')
                ->get();

            $urls = [];

            // Static pages
            $urls[] = [
                'loc' => route('home'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];

            $urls[] = [
                'loc' => route('products.index'),
                'lastmod' => $products->max('updated_at')?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ];

            foreach ($categories as $category) {
                $urls[] = [
                    'loc' => route('products.index', ['category' => $category->slug]),
                    'lastmod' => $category->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }

            foreach ($products as $product) {
                $urls[] = [
                    'loc' => route('products.show', $product->slug),
                    'lastmod' => $product->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }

            return view('sitemap', ['urls' => $urls])->render();
        });

        return response($xml, 200, [
            'Content-Type' => 'text/xml',
        ]);
    }

    public function robots(): Response
    {
        $sitemapUrl = URL::to('/sitemap.xml');

        $content = "User-agent: *\nAllow: /\nSitemap: {$sitemapUrl}\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
