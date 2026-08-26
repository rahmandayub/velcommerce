<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::forget('sitemap.xml');
});

test('sitemap returns valid xml with products and categories', function (): void {
    $category = Category::factory()->create(['is_active' => true, 'slug' => 'elektronik']);
    $product = Product::factory()->create(['is_active' => true, 'slug' => 'produk-test-sitemap']);

    $response = $this->get(route('sitemap'));
    $response->assertOk();
    $response->assertHeader('content-type', 'text/xml; charset=UTF-8');

    $content = $response->getContent();
    expect($content)->toContain('<?xml version="1.0" encoding="UTF-8"?>');
    expect($content)->toContain('<urlset');
    expect($content)->toContain(route('home'));
    expect($content)->toContain(route('products.index'));
    expect($content)->toContain(route('products.show', $product->slug));
    expect($content)->toContain(route('products.index', ['category' => $category->slug]));
});

test('sitemap excludes inactive products', function (): void {
    $inactive = Product::factory()->inactive()->create();

    $response = $this->get(route('sitemap'));
    $response->assertOk();

    expect($response->getContent())->not->toContain(route('products.show', $inactive->slug));
});

test('robots txt points to sitemap', function (): void {
    $response = $this->get(route('robots'));
    $response->assertOk();
    $response->assertHeader('content-type', 'text/plain; charset=UTF-8');

    $content = $response->getContent();
    expect($content)->toContain('User-agent: *');
    expect($content)->toContain('Allow: /');
    expect($content)->toContain('/sitemap.xml');
});

test('product detail inertia response contains seo and json-ld props', function (): void {
    $product = Product::factory()->create([
        'is_active' => true,
        'name' => 'Produk SEO Test',
        'meta_title' => 'Judul SEO Kustom',
        'meta_description' => 'Deskripsi SEO kustom untuk testing.',
    ]);

    $response = $this->get(route('products.show', $product->slug));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('products/show')
        ->has('seo')
        ->where('seo.title', 'Judul SEO Kustom')
        ->where('seo.description', 'Deskripsi SEO kustom untuk testing.')
        ->has('jsonLd')
        ->has('breadcrumbLd')
    );
});

test('product seo falls back to name and short description when meta is empty', function (): void {
    $product = Product::factory()->create([
        'is_active' => true,
        'name' => 'Fallback SEO Product',
        'meta_title' => null,
        'meta_description' => null,
        'short_description' => 'Short fallback desc',
    ]);

    $response = $this->get(route('products.show', $product->slug));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('seo.title', 'Fallback SEO Product')
        ->where('seo.description', 'Short fallback desc')
    );
});
