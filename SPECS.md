# Brief Proyek: E-Commerce (velcommerce)

## Ringkasan

Proyek e-commerce sebagai portfolio, dibangun sebagai monolith menggunakan Laravel + Inertia.js + React, dengan starter kit resmi Laravel yang sudah menyertakan autentikasi.

---

## Stack Teknologi

| Layer      | Teknologi                                                                                  |
| ---------- | ------------------------------------------------------------------------------------------ |
| Backend    | Laravel 13, PHP 8.5                                                                        |
| Frontend   | Inertia.js 3 + React 19 + TypeScript                                                       |
| UI         | Tailwind v4 + shadcn/ui (bawaan starter kit)                                               |
| Auth       | Laravel Fortify (via starter kit) — login, register, verifikasi email, reset password, 2FA |
| Database   | MySQL / PostgreSQL                                                                         |
| Payment    | Midtrans / Xendit (Sandbox)                                                                |
| Testing    | Pest 4                                                                                     |
| Deployment | Laravel Forge / Railway / Laravel Cloud                                                    |

**Catatan kompatibilitas:** Laravel 13 minimum membutuhkan PHP 8.3, dan resmi mendukung hingga PHP 8.5 — jadi kombinasi Laravel 13 + PHP 8.5 valid dan direkomendasikan.

---

## Setup Awal

```bash
composer create-project laravel/laravel ecommerce-portfolio
cd ecommerce-portfolio
composer require laravel/fortify
php artisan install:react   # starter kit resmi Inertia + React
```

> Cek `laravel.com/docs/13.x/starter-kits` saat eksekusi — command installer starter kit bisa berubah nama seiring update Laravel.

---

## Fitur

### Wajib (dasar e-commerce)

- Katalog produk dengan kategori, filter, dan search
- Detail produk: galeri gambar, varian (ukuran/warna), stok
- Keranjang belanja (persist di session/DB, bukan cuma localStorage)
- Checkout multi-step + integrasi payment gateway asli (Midtrans/Xendit)
- Auth (register/login, verifikasi email, reset password)
- Riwayat pesanan & tracking status order

### Pembeda (nilai jual ke klien)

- Admin dashboard dengan analytics (grafik penjualan, produk terlaris)
- Manajemen stok otomatis + alert stok rendah
- Review & rating produk
- Wishlist
- Notifikasi email (order berhasil, status berubah — via queue)
- Multi-role (admin, seller, customer)
- Diskon/voucher/kupon

### Polish teknis

- Responsive penuh
- Optimasi gambar & lazy loading
- SEO dasar (meta tag dinamis, structured data, sitemap)
- Testing (minimal feature test untuk checkout flow)
- CI/CD sederhana
- Dokumentasi README dengan screenshot/demo link

---

## Roadmap Pengerjaan (5 Minggu)

### Fase 1 — Fondasi (Minggu 1)

- Setup starter kit, konfigurasi Fortify (email verification + 2FA opsional)
- Skema database inti: `users`, `products`, `categories`, `product_variants`, `carts`, `cart_items`, `orders`, `order_items`, `addresses`
- Role & permission dasar (admin vs customer) — pakai `spatie/laravel-permission`
- Layout dasar: navbar, footer, halaman auth (varian simple/card/split dari starter kit)

### Fase 2 — Core Commerce (Minggu 2–3)

- CRUD produk (admin) + upload multi-gambar
- Halaman katalog: filter kategori, search, pagination (Inertia partial reload)
- Halaman detail produk + pemilihan varian
- Cart (state di backend, sync via Inertia shared props)
- Checkout flow multi-step + integrasi payment gateway sandbox
- Order history & tracking status (pending → paid → shipped → completed)

### Fase 3 — Fitur Pembeda (Minggu 4)

- Admin dashboard: chart penjualan (Recharts), produk terlaris, stok rendah
- Review & rating produk
- Wishlist
- Voucher/diskon
- Notifikasi email (queue job)

### Fase 4 — Polish & Deploy (Minggu 5)

- SEO: meta tag dinamis per produk, sitemap, Inertia SSR (`build:ssr`)
- Optimasi gambar (lazy load, resize otomatis via Intervention Image)
- Testing: feature test untuk flow checkout & auth (Pest)
- CI sederhana (GitHub Actions: run test tiap push)
- Deploy ke staging + custom domain, screenshot & demo credentials untuk README

---

## Tips Kualitas "Senior-Level"

- Pakai **Form Request** untuk validasi, bukan validasi inline di controller
- Pakai **Policy** untuk otorisasi (misal hanya admin yang boleh edit produk)
- Manfaatkan **Inertia shared data** (`HandleInertiaRequests`) untuk auth state & flash message
- Tulis README dengan diagram ERD sederhana
