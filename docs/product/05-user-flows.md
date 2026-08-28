# 05 — User Flows: VelCommerce Core Loop

## Flow 1: Customer Discovers & Purchases Handmade Ceramic (Primary)

```
HOME
 │  Hero: MADE BY HAND. MADE TO MATTER. [Explore Collection]
 │  Explore by Craft
 ▼
CRAFT: /craft/ceramics
 │  Filter: Availability, Location | Sort: Curated
 │  Grid: Product cards (image, name, maker byline, price, availability badge)
 ▼
PRODUCT: /products/hand-thrown-mug-natural-glaze
 │  Gallery | Title: Hand-thrown ceramic mug | By Mawar Studio — Yogyakarta
 │  Price Rp185.000 | Badge: Ready Stock • 3 left
 │  [Add to Cart]
 │  About this piece | Materials | Made by Mawar Studio [View Maker →]
 │  Craft Process timeline
 ▼
MAKER STORE (optional branch): /makers/mawar-studio
 │  Cover + Avatar + Story + Location + Specialty: Ceramics
 │  Products from this maker
 │  [Back to Product] or [Explore other Maker products]
 ▼
CART: /cart
 │  Line items grouped by Maker (opsional untuk portfolio: show multi-maker)
 │  Qty adjust, availability warning if made-to-order
 ▼
CHECKOUT: /checkout/address → /checkout/confirm → POST /checkout/store
 │  Address + Notes | Confirm: production_time displayed for made-to-order items
 ▼
ORDER CONFIRMATION: /checkout/success + /orders/{id}
 │  Status: pending → paid → shipped → completed
 ▼
REVIEW: /orders/{id} → Review product & maker
```

**Decision points:**
- Jika `made_to_order`: Cart & Confirm tampilkan `Estimasi pembuatan 14 hari` jelas sebelum bayar.
- Jika `ready_stock` low (≤3): tampilkan `Only 3 left` bukan urgency merah, tapi honest badge.

## Flow 2: Maker Onboarding & Product Creation

```
REGISTER → CREATE MAKER PROFILE
 │  store_name, slug, craft_specialty, location, story, avatar
 ▼
PENDING VERIFICATION (is_verified = false)
 │  Admin queue: /admin/makers → Approve
 ▼
MAKER DASHBOARD: /maker/dashboard
 │  Stats: products, orders pending
 ▼
CREATE PRODUCT: /maker/products/create
 │  name, category (craft), price, availability, stock/production_time,
 │  materials (tags), craft_method, origin, story, process_steps, images
 ▼
PRODUCT LIST: /maker/products
 │  is_curated badge (admin curated)
```

## Flow 3: Admin Curation

```
ADMIN: /admin/makers → Verify Maker
ADMIN: /admin/products → Toggle is_curated / is_featured
→ Product muncul di Home (Curated Products / Featured Makers)
```

## Edge Cases untuk Portfolio

- Guest cart → login → merge (sudah ada, pertahankan)
- Checkout dengan mixed availability (ready + made-to-order) → tampilkan split estimasi
- Review hanya jika order completed & user sudah beli (sudah ada policy)

Next: Page Inventory & Wireframe
