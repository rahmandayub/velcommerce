# 03 — User / Business Goals & Actor Flows

## 1. Customer Goals (Buyer)

| Goal | Success Signal | UX Implication |
|------|----------------|----------------|
| Menemukan barang unik tanpa tahu nama produknya | Klik Explore Craft → Product, bukan hanya Search | Home harus inspirasi-first |
| Memahami cerita & kualitas sebelum beli | Bisa sebutkan maker, material, origin setelah lihat PDP | PDP butuh Maker block + Materials + Process |
| Percaya bahwa barang handmade worth the price | Lihat availability & production_time jelas, bukan stok 999 | Transparansi stok small-batch |
| Checkout tanpa ragu untuk made-to-order | Paham estimasi 14 hari sebelum bayar | Availability badge di atas CTA |

**Jobs To Be Done:**
- "Ketika saya mau dekor rumah, saya ingin menemukan objek yang punya karakter, agar rumah terasa personal."
- "Ketika saya mau kasih hadiah, saya ingin cerita di balik barangnya, agar hadiah terasa bermakna."

## 2. Maker Goals (Seller)

| Goal | Success Signal | System Implication |
|------|----------------|---------------------|
| Daftar dan buka studio dengan kurasi | Submit → pending verification → approved | `makers.is_verified` gate |
| Upload produk small-batch dengan jujur | Isi availability + production_time tanpa dipaksa stock 100 | Form Product dengan enum availability |
| Dapat order dan fulfill tanpa sistem kompleks | Lihat Orders → update status shipped | Dashboard minimal, bukan ERP |
| Bangun reputasi | Review terakumulasi di Maker profile | Rating agregat via products |

**Jobs To Be Done:**
- "Ketika saya selesai bikin batch kecil, saya ingin upload 3-5 produk dengan cerita, agar bisa ditemukan tanpa perang harga."

## 3. Admin/Curator Goals

- Menjaga kurasi: hanya maker terverifikasi yang tampil di Featured.
- Moderasi produk: `is_curated` untuk Home.
- Tidak perlu analytics kompleks di MVP, cukup Products & Makers queue.

## 4. Business Goals (VelCommerce sebagai Platform)

- **Curated, not commodity:** Metric = % produk dengan maker story lengkap > 80%
- **Discovery over search:** Metric = ratio Explore Craft clicks vs Search usage
- **Trust:** Metric = review rate untuk made-to_order tetap tinggi (transparansi lead time)

## 5. Actor Flows (Simplified)

### Customer
```
[Home: Discover] → [Explore by Craft] → [Category Listing] → [Product Detail (+Maker)] 
→ [Cart] → [Checkout: Address → Confirm → Store] → [Order Confirmation] → [Order History + Review]
                    ↘ [Maker Store] → [Maker Profile]
```

### Maker
```
Register → Create Maker Profile (pending) → [Admin Verify] → Dashboard
→ Add Product (availability, materials, story, images)
→ Manage Products (edit stock/production_time)
→ Receive Order → Fulfill (shipped) → Receive Review
```

### Admin
```
Dashboard Queue → Review Maker (verify/reject) → Moderate Product (curate)
→ Manage Categories → Monitor Orders
```

## 6. Keputusan UX dari Goals

1.  **Maker adalah first-class citizen:** Link "View Maker →" harus di atas lipatan PDP, bukan di footer.
2.  **Availability adalah primary info:** Badge `Ready Stock • 3 left` atau `Made to Order • 14 hari` di dekat harga & CTA.
3.  **Tidak ada hard sell:** Tombol `Add to Cart` tidak didominasi urgency countdown.

Next: `04-information-architecture.md` — terjemahkan goals ini ke struktur URL & navigasi.
