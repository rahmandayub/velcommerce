# 04 — Information Architecture: VelCommerce

## 1. Sitemap (Customer Facing)

```
/ (Home)
├── /craft/{slug}          → Category Listing (ex: /craft/ceramics)
├── /products/{slug}       → Product Detail
├── /makers/{slug}         → Maker Store (listing produk maker)
├── /makers/{slug}/about   → Maker Profile (story, location, craft specialty)
├── /search?q=             → Search (product + maker + material)
├── /cart
├── /checkout
│   ├── /checkout/address
│   ├── /checkout/confirm
│   └── /checkout/success
├── /orders                → Order History
├── /orders/{id}           → Order Detail
├── /wishlist
└── /profile, /settings
```

**Maker Dashboard (auth: maker)**
```
/maker/dashboard
├── /maker/products
├── /maker/products/create
├── /maker/products/{id}/edit
├── /maker/orders
└── /maker/store/edit
```

**Admin (auth: admin)**
```
/admin
├── /admin/makers          → verification queue
├── /admin/products        → curation queue
├── /admin/categories
└── /admin/orders
```

## 2. Navigation Model

**Top Nav (StorefrontLayout):**
- Left: Logo VelCommerce
- Center: Explore Craft [Ceramics | Textile | Wood | Jewelry | Leather | Home | Art] — bukan dropdown generik
- Right: Search (icon → expand), Wishlist, Cart, User

**Home Sections (urutan terkunci):**
1. Navigation
2. Editorial Hero — `MADE BY HAND. MADE TO MATTER.` + Featured Maker/Product
3. Explore by Craft — 7 craft tiles
4. Featured Makers — 4-6 maker cards (avatar, location, specialty)
5. Curated Products — `Selected pieces for your space` (8 products, filter is_curated)
6. Craft Story — editorial content: `From raw clay to your table` (1 large story)
7. New Arrivals / Weekly Drop — chronological, not sale
8. CTA — `Discover the makers behind the things you love.` + Explore Collection

## 3. URL & SEO Decisions

- Category pakai `/craft/{slug}` bukan `/categories/{slug}` — bahasa craft lebih editorial.
- Product tetap `/products/{slug}` untuk kompatibilitas, tapi breadcrumb: Home > Ceramics > Mug
- Maker pakai `/makers/{slug}` — konsisten dengan domain.

## 4. Filtering & Sorting

**Category Listing (`/craft/ceramics`):**
- Filter: Availability (Ready Stock / Made to Order), Price range, Location (city), Material (chips)
- Sort: Curated first, Newest, Price low-high
- Tidak ada sort by "popular / flash sale"

**Search:**
- Query across: product.name, product.materials, maker.store_name
- Empty state: tampilkan Explore Craft, bukan "produk tidak ditemukan" kosong.

## 5. Content Hierarchy per Page

**Product Detail (PDP):**
```
Images (gallery)
→ Title + Maker byline (linked)
→ Location + Craft Method
→ Price + Availability badge (Ready Stock / Made to Order • 14 hari)
→ Add to Cart
→ About this piece (story)
→ Materials
→ Made by (Maker card → View Maker)
→ Craft Process (timeline)
→ Reviews
→ More from this Maker
```

Maker block di atas lipatan — bukan di bawah reviews.

Next: `05-user-flows.md` + `06-page-inventory.md`
