# 01 — Product Definition: VelCommerce

> Status: DRAFT → perlu di-lock sebelum lanjut ke Domain Model

## 1. Positioning Statement

**VelCommerce is a curated marketplace for independent Indonesian makers and artisans.**

Tagline: **Temukan Karya. Kenali Pembuatnya.**
English: *A marketplace for things worth discovering.*

**One-liner portfolio:**
> VelCommerce mempertemukan pembeli yang mencari barang bermakna dengan pengrajin lokal yang membuat produk small-batch, bukan produksi massal.

Bukan: "Marketplace tempat kamu membeli berbagai macam barang."
Tapi: "Tempat menemukan barang yang dibuat, bukan diproduksi."

## 2. Problem & Opportunity

**Problem buyer:** Marketplace general (Shopee/Tokopedia) meng homogenisasi produk. Keramik handmade diperlakukan sama dengan casing HP massal: `name - price - stock - Add to Cart`. Cerita, material, teknik, dan maker-nya hilang.

**Problem maker:** Pengrajin independen kesulitan membangun trust & discovery tanpa tenggelam di lautan produk massal. Mereka butuh etalase editorial, bukan listing komoditas.

**Opportunity:** Ekonomi kreatif Indonesia (fashion & kriya) adalah subsektor besar yang didorong pemerintah untuk masuk ekosistem digital. Preseden curated artisan market sudah tervalidasi. VelCommerce mengambil celah **curated, bukan general**.

## 3. Target Audience (Portfolio Scope)

**Primary — Buyer (Customer):**
- 22-38 tahun, urban (Jakarta, Bandung, Jogja, Bali, Surabaya)
- Menghargai design, material, dan cerita di balik produk
- Mencari hadiah bermakna, dekor rumah, fashion statement — bukan barang termurah
- Perilaku: `browsing untuk inspirasi` > `search untuk kebutuhan spesifik`

**Secondary — Maker (Seller):**
- Studio independen / perorangan (bukan pabrik)
- Contoh: Mawar Studio (keramik Jogja), Kana Weaves (tenun Lombok), Kayu Rupa (woodcraft Jepara)
- Butuh: etalase yang menghargai proses, manajemen stok small-batch, pre-order

**Tertiary — Admin/Curator:**
- Kurator VelCommerce yang memverifikasi maker dan menjaga kualitas kurasi.

## 4. Category Strategy (6-8 saja)

Jangan 30 kategori. Untuk portfolio, sempit tapi believable:

| # | Craft | Contoh Produk | Alasan Portfolio |
|---|-------|---------------|------------------|
| 1 | Ceramics | Mug, vase, piring stoneware | Visual kuat, varian glaze |
| 2 | Textile | Batik artisan, ecoprint, tenun | Story & origin kuat |
| 3 | Woodcraft | Piring kayu, stool, decor | Material & technique |
| 4 | Jewelry | Perak handmade, kuningan | Small, high value |
| 5 | Leather Goods | Tas anyam, dompet kulit | Craft + durability |
| 6 | Home & Living | Anyaman, macrame, decor | Curated for space |
| 7 | Art | Print, artwork kecil | Maker-centric |

> Keputusan: 7 kategori untuk MVP. Tidak ada `Elektronik`, `Pakaian Pria/Wanita` generik.

## 5. What VelCommerce IS vs IS NOT

| VelCommerce IS | VelCommerce IS NOT |
|----------------|--------------------|
| Curated (maker diverifikasi) | Open marketplace (semua orang bisa jual) |
| Small-batch & handmade | Mass-produced & dropship |
| Story-driven (maker, material, process) | Spec-driven (hanya harga & stok) |
| Availability: ready stock + made-to-order | Selalu ready stock 100+ |
| Discovery: Explore by Craft / Maker | Search bar + flash sale sebagai hero |
| Editorial & calm (Teal Editorial) | Ramai, diskon, countdown |

**Hard constraints:**
- Tidak ada Flash Sale. Ganti dengan `Weekly Drop` / `Small Batch`.
- Tidak ada `Gratis Ongkir 0 Rupiah` sebagai value prop utama. Value prop adalah `Keaslian & Cerita`.
- Setiap Product wajib terhubung ke Maker.

## 6. Value Proposition

**Untuk Buyer:**
- Temukan barang unik yang tidak ada di tempat lain.
- Kenali siapa yang membuat, dari mana asalnya, dan bagaimana dibuatnya.
- Beli dengan percaya karena kurasi & transparansi.

**Untuk Maker:**
- Etalase premium yang menghargai karya, bukan perang harga.
- Tools sederhana: kelola produk small-batch, pre-order, dan cerita studio.

**Untuk Portfolio:**
- Menunjukkan kemampuan menerjemahkan domain bisnis menjadi domain model & UX.

## 7. Core Experience (North Star)

Bukan:
```
Search → Product → Buy
```

Tapi:
```
Discover → Explore Craft → Meet the Maker → Understand the Craft → Purchase
```

## 8. Brand Pillars (sinkron dengan Teal Editorial)

- **Calm & Curated:** Whitespace, grid editorial
- **Human & Warm:** Foto proses, tangan pembuat
- **Honest:** Transparansi material, origin, lead time
- **Premium Accessible:** Boutique tapi tidak intimidatif

## 9. Out of Scope MVP

- Lelang / Offer system
- Chat real-time buyer-maker
- Subscription / membership
- Multi-warehouse inventory
- Secondhand / vintage (simpan untuk v2)

## 10. Decisions to Lock

1. Setuju 7 kategori di atas?
2. Setuju hilangkan Flash Sale & kategori generik?
3. Setuju Product wajib punya Maker?

Jika 3 ini di-lock, kita bisa turun ke `02-domain-model.md` tanpa revisi bolak-balik.

---
**Next:** `02-domain-model.md`
