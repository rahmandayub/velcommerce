# 06 — Page Inventory: VelCommerce

## Customer Pages

| Page | Route | Priority | Notes |
|------|-------|----------|-------|
| Home | `/` | 🔴 Core | 8 sections terkunci (Hero → CTA) |
| Craft Listing | `/craft/{slug}` | 🔴 Core | Filter availability, location |
| Product Listing (search) | `/search?q=` | 🟡 | Search product + maker + material |
| Product Detail | `/products/{slug}` | 🔴 Core | Maker block di atas lipatan |
| Maker Store | `/makers/{slug}` | 🔴 Core | Listing produk per maker |
| Maker Profile | `/makers/{slug}/about` | 🔴 Core | Story, location, specialty |
| Cart | `/cart` | 🔴 Core | Group by maker, badge availability |
| Checkout Address | `/checkout/address` | 🔴 Core |  |
| Checkout Confirm | `/checkout/confirm` | 🔴 Core | Show production_time |
| Order Success | `/checkout/success` | 🔴 Core |  |
| Order History | `/orders` | 🟡 |  |
| Order Detail | `/orders/{id}` | 🟡 | Timeline status |
| Wishlist | `/wishlist` | 🟡 |  |
| Profile/Settings | `/profile` | 🟡 |  |

## Maker Pages

| Page | Route | Priority | Notes |
|------|-------|----------|-------|
| Maker Dashboard | `/maker/dashboard` | 🔴 | Stats + recent orders |
| Maker Products | `/maker/products` | 🔴 | List + is_curated status |
| Create Product | `/maker/products/create` | 🔴 | Form availability-aware |
| Edit Product | `/maker/products/{id}/edit` | 🔴 |  |
| Maker Orders | `/maker/orders` | 🔴 | Fulfill → shipped |
| Store Profile Edit | `/maker/store/edit` | 🔴 | Edit story, location, cover |

## Admin Pages

| Page | Route | Priority | Notes |
|------|-------|----------|-------|
| Admin Dashboard | `/admin` | 🟡 | Queue counts |
| Makers Queue | `/admin/makers` | 🔴 | Verify |
| Products Queue | `/admin/products` | 🔴 | Curate |
| Categories | `/admin/categories` | 🟡 | Manage 7 crafts |
| Orders | `/admin/orders` | 🟡 | Monitor |

🔴 = wajib untuk portfolio MVP
🟡 = secondary, bisa setelah core loop solid

## Build Order (Sequential)

1. Home (grayscale) → 2. Craft Listing → 3. Product Detail + Maker Store → 4. Cart/Checkout → 5. Maker Dashboard
