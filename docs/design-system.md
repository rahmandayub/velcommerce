# Velcommerce — Design System

> **Reference:** `A selection of premium styles` by **@by.illarion**  
> **Theme:** *Premium Teal Collection* — minimal, editorial, luxury commerce  
> **Date:** 28 Aug 2026  
> **Version:** 1.0

---

## 1. Prinsip Desain

| Prinsip | Deskripsi |
|---------|-----------|
| **Premium Minimal** | Banyak whitespace, grid ketat, tanpa ornamen berlebih. Warna jadi hero. |
| **Editorial Contrast** | Blok warna solid penuh (full-bleed) — Deep Lagoon gelap vs Aqua Silk terang, dipisah Teal Wave sebagai aksen. |
| **High Legibility** | Headings ultra-bold condensed uppercase, body `Instrument Sans` 400/500. Line-height longgar. |
| **Consistent Radius** | `0.625rem` (10px) untuk card/button, `9999px` untuk pill. Tidak ada radius acak. |
| **Accessible First** | Semua pasangan teks/background ≥ 4.5:1 (WCAG AA). Fokus ring selalu visible. |

Mood seperti referensi: kiri vertikal gelap (Deep Lagoon), kanan atas vibrant (Teal Wave), kanan bawah soft (Aqua Silk). Layout di Velcommerce mengikuti — **header/footer = Deep Lagoon**, **CTA/aksen = Teal Wave**, **background page/card = Aqua Silk / White**.

---

## 2. Palet Warna — Foundational

### 2.1 Core 3 Warna

| Token | Nama | HEX | RGB | OKLCH | Preview | Penggunaan Utama |
|-------|------|-----|-----|-------|---------|------------------|
| `deep-lagoon` | **Deep Lagoon** | `#0F2A2A` | `15,42,42` | `oklch(0.264 0.033 195.35)` | █ `#0F2A2A` | Background gelap, navbar, footer, teks di atas Teal/Aqua, `foreground`, `primary-foreground` (dark) |
| `teal-wave` | **Teal Wave** | `#00BFA6` | `0,191,166` | `oklch(0.720 0.132 178.85)` | █ `#00BFA6` | **Primary** — CTA, badge, link hover, chart-2, ring, icon aksen. *Jangan pakai sebagai warna teks di atas putih/Aqua.* |
| `aqua-silk` | **Aqua Silk** | `#D9FAF4` | `217,250,244` | `oklch(0.960 0.035 183.41)` | █ `#D9FAF4` | Background terang, card, muted, secondary, `background` light theme |

**Typography di referensi:**
- Di atas `Deep Lagoon` → teks `Teal Wave` (`#00BFA6` on `#0F2A2A` = **6.50:1** ✅ AA)
- Di atas `Teal Wave` → teks `#0F2A2A` / `black` (`#00BFA6` on `#0F2A2A` kebalikan sama, `black` on `#00BFA6` = **9.00:1** ✅)
- Di atas `Aqua Silk` → teks `#0F2A2A` / `black` (`#D9FAF4` on `#0F2A2A` = **13.67:1** ✅ AAA)

> ⚠️ `Teal Wave` on `Aqua Silk` = **2.10:1** ❌, `Teal Wave` on `White` = **2.33:1** ❌ — jangan pakai Teal sebagai warna body-text di background terang.

### 2.2 Scale Turunan (untuk hover, border, disabled)

| Base | 50 | 100 | 200 | 300 | 400 (base) | 500 | 600 | 700 | 800 | 900 |
|------|----|-----|-----|-----|------------|-----|-----|-----|-----|-----|
| **Deep Lagoon** | `#E7EBEB` | `#C9D1D1` | `#98A7A7` | `#677E7E` | `#0F2A2A` | `#0D2525` | `#0B201F` | `#081817` | `#0A1F1F` | `#061414` |
| **Teal Wave** | `#E6F9F6` | `#CCF2ED` | `#99E6DC` | `#66D9CA` | `#00BFA6` | `#00AB95` | `#009984` | `#008673` | `#007362` | `#006051` |
| **Aqua Silk** | `#FAFFFE` | `#F5FFFD` | `#EDFFFB` | `#E4FFF9` | `#D9FAF4` | `#C3E1DB` | `#AEC8C3` | `#98AFAA` | `#829692` | `#6C7D79` |

Generate dengan `color-mix(in oklch, var(--color) 90%, white/black)` — lihat implementasi CSS.

### 2.3 Semantic Mapping (shadcn/ui + Tailwind v4)

> File target: `resources/css/app.css` — variabel `--background`, `--foreground`, dst.

#### Light Theme (`:root` — default)

```css
:root {
  /* Base reference */
  --deep-lagoon: oklch(0.264 0.033 195.35); /* #0F2A2A */
  --teal-wave: oklch(0.720 0.132 178.85);   /* #00BFA6 */
  --aqua-silk: oklch(0.960 0.035 183.41);   /* #D9FAF4 */

  /* Semantic - light */
  --background: oklch(0.985 0.008 183);              /* hampir white, sedikit tint Aqua */
  --foreground: var(--deep-lagoon);

  --card: oklch(1 0 0);                             /* white */
  --card-foreground: var(--deep-lagoon);

  --popover: oklch(1 0 0);
  --popover-foreground: var(--deep-lagoon);

  --primary: var(--teal-wave);                      /* CTA Teal */
  --primary-foreground: var(--deep-lagoon);         /* teks Deep Lagoon di atas Teal - kontras 6.5:1 */

  --secondary: oklch(0.94 0.03 183);                 /* Aqua Silk muted - untuk filter chip, secondary button */
  --secondary-foreground: var(--deep-lagoon);

  --muted: var(--aqua-silk);                        /* #D9FAF4 */
  --muted-foreground: oklch(0.45 0.03 195);          /* Deep Lagoon 60% - untuk description */

  --accent: var(--aqua-silk);
  --accent-foreground: var(--deep-lagoon);

  --destructive: oklch(0.577 0.245 27.325);
  --destructive-foreground: oklch(0.985 0 0);

  --border: oklch(0.88 0.02 183);                    /* Aqua tint border */
  --input: oklch(0.88 0.02 183);
  --ring: var(--teal-wave);

  --radius: 0.625rem;

  --chart-1: var(--teal-wave);
  --chart-2: var(--deep-lagoon);
  --chart-3: oklch(0.65 0.12 178);
  --chart-4: oklch(0.8 0.08 183);
  --chart-5: oklch(0.45 0.05 195);

  --sidebar: var(--deep-lagoon);
  --sidebar-foreground: var(--aqua-silk);
  --sidebar-primary: var(--teal-wave);
  --sidebar-primary-foreground: var(--deep-lagoon);
  --sidebar-accent: oklch(0.32 0.03 195);
  --sidebar-accent-foreground: var(--aqua-silk);
  --sidebar-border: oklch(0.32 0.03 195);
  --sidebar-ring: var(--teal-wave);
}
```

#### Dark Theme (`.dark`)

```css
.dark {
  --background: var(--deep-lagoon);                 /* #0F2A2A full-bleed */
  --foreground: var(--aqua-silk);                   /* #D9FAF4 */

  --card: oklch(0.30 0.03 195);                      /* Deep Lagoon +4% lightness */
  --card-foreground: var(--aqua-silk);

  --popover: oklch(0.30 0.03 195);
  --popover-foreground: var(--aqua-silk);

  --primary: var(--teal-wave);
  --primary-foreground: var(--deep-lagoon);

  --secondary: oklch(0.32 0.03 195);
  --secondary-foreground: var(--aqua-silk);

  --muted: oklch(0.32 0.03 195);
  --muted-foreground: oklch(0.75 0.02 183);

  --accent: oklch(0.32 0.03 195);
  --accent-foreground: var(--aqua-silk);

  --destructive: oklch(0.396 0.141 25.723);
  --destructive-foreground: var(--aqua-silk);

  --border: oklch(0.32 0.03 195);
  --input: oklch(0.32 0.03 195);
  --ring: var(--teal-wave);

  --chart-1: var(--teal-wave);
  --chart-2: oklch(0.75 0.08 183);
  --chart-3: oklch(0.55 0.10 178);
  --chart-4: oklch(0.65 0.09 195);
  --chart-5: oklch(0.45 0.05 195);

  --sidebar: oklch(0.22 0.03 195);
  --sidebar-foreground: var(--aqua-silk);
  --sidebar-primary: var(--teal-wave);
  --sidebar-primary-foreground: var(--deep-lagoon);
  --sidebar-accent: oklch(0.32 0.03 195);
  --sidebar-accent-foreground: var(--aqua-silk);
  --sidebar-border: oklch(0.32 0.03 195);
  --sidebar-ring: var(--teal-wave);
}
```

> Sudah di-`@theme` mapping otomatis via `app.css` existing:
> `--color-background: var(--background)` dst. Tidak perlu ubah `@theme` block, cukup ganti isi `:root` & `.dark`.

---

## 3. Tipografi

### 3.1 Font Stack

| Role | Font | Weight | Usage |
|------|------|--------|-------|
| **Sans (default)** | `Instrument Sans` (sudah via `laravel-vite-plugin/fonts` bunny) | 400, 500, 600 | Body, UI, label, nav |
| **Display (heading)** | `Instrument Sans` — dengan `tracking-tighter uppercase` | 600-700 | H1-H2 hero, section title (meniru referensi `TEAL WAVE` condensed) |
| **Mono (opsional)** | `Geist Mono` / `JetBrains Mono` | 400 | SKU, kode kupon, harga |

Tidak perlu tambah font baru untuk MVP — cukup manfaatkan `Instrument Sans` yang sudah ada. Jika ingin lebih editorial, tambah `Newsreader` atau `Fraunces` untuk display (opsional phase 2).

### 3.2 Scale

| Level | Class | Size | Line-height | Weight | Tracking | Transform |
|-------|-------|------|-------------|--------|----------|-----------|
| Display | `text-5xl md:text-6xl` | 48-60px | 1.0 | 700 | `-0.04em` | `uppercase` |
| H1 | `text-3xl md:text-4xl` | 30-36px | 1.1 | 700 | `-0.03em` | `uppercase` / `none` |
| H2 | `text-2xl md:text-3xl` | 24-30px | 1.2 | 600 | `-0.02em` | `none` |
| H3 | `text-xl` | 20px | 1.3 | 600 | `-0.015em` | `none` |
| Body | `text-sm md:text-base` | 14-16px | 1.6 | 400 | `0` | `none` |
| Small | `text-xs` | 12px | 1.5 | 400 | `0.02em` | `none` |
| Label (referensi `premium styles`) | `text-[10px] md:text-xs` | 10-12px | 1.4 | 400 | `0.3em` | `uppercase` |
| Price | `text-lg font-semibold` | 18px | 1.2 | 600 | `-0.01em` | `none` |

**Contoh hero meniru referensi:**

```tsx
// resources/js/pages/welcome.tsx atau hero section
<p className="text-[11px] tracking-[0.35em] uppercase text-teal-wave">A selection of</p>
<h1 className="font-bold tracking-tighter uppercase text-deep-lagoon leading-none">
  DEEP<br/>LAGOON
</h1>
<span className="text-xs tracking-widest text-deep-lagoon/60">#0F2A2A</span>
```

Gunakan utility arbitrary: `text-[#0F2A2A]` / `bg-[#00BFA6]` untuk swatch, atau `bg-primary`, `text-primary` setelah token di-apply.

### 3.3 Aturan

- Heading selalu `tracking-tight` atau `tighter` + `font-bold`.
- Label kecil (`premium styles` di referensi) → `tracking-[0.3em] uppercase text-muted-foreground`.
- Jangan gunakan `font-light` untuk body — minimal 400.
- Harga pakai `tabular-nums` agar rata: `className="tabular-nums"`.

---

## 4. Spacing, Radius, Shadow, Motion

### 4.1 Spacing

Gunakan skala Tailwind default `4 = 1rem`. Section vertical rhythm: `py-12 md:py-20`, card gap `gap-4 md:gap-6`.

### 4.2 Radius

| Token | Value | Usage |
|-------|-------|-------|
| `--radius` | `0.625rem` (10px) | Default (card, input, select) |
| `--radius-lg` | `var(--radius)` | Card besar |
| `--radius-md` | `calc(var(--radius) - 2px)` | Button |
| `--radius-sm` | `calc(var(--radius) - 4px)` | Badge |
| `full` | `9999px` | Pill (`@by.illarion` di referensi → `rounded-full border`) |

### 4.3 Shadow / Elevation

Flat minimal — referensi tanpa shadow, hanya blok warna. Untuk Velcommerce:

- `shadow-sm` untuk card di light mode saja.
- Di dark mode: gunakan `border` tanpa shadow.
- Focus ring: `ring-2 ring-ring ring-offset-2` warna Teal Wave.

### 4.4 Motion

- `duration-200 ease-out` untuk hover.
- Button hover: `hover:brightness-105` (karena Teal sudah saturated) atau `hover:bg-teal-600` (`#009984`).
- Tidak ada animasi berlebihan — premium = tenang.

---

## 5. Komponen UI (shadcn/ui + Velcommerce)

Semua komponen sudah ada di `resources/js/components/ui/*` (`new-york` style). Mapping berikut wajib diikuti:

### 5.1 Button

| Variant | Classes (Tokens) | Usage |
|---------|------------------|-------|
| `default` | `bg-primary text-primary-foreground hover:bg-[#009984] shadow-sm` | CTA utama — Add to Cart, Checkout, Place Order |
| `secondary` | `bg-secondary text-secondary-foreground hover:bg-muted` | Secondary action, filter reset |
| `outline` | `border border-input bg-transparent hover:bg-accent hover:text-accent-foreground` | Ghost + border Aqua |
| `ghost` | `hover:bg-accent hover:text-accent-foreground` | Icon button, nav |
| `destructive` | `bg-destructive text-destructive-foreground` | Delete, cancel order |
| Pill (khusus) | `rounded-full border border-[#00BFA6]/30 text-[#00BFA6] px-6 py-2 text-sm` | Meniru `@by.illarion` di referensi — untuk credit / tag editorial |

```tsx
<Button>Tambah ke Keranjang</Button> {/* primary Teal */}
<Button variant="secondary">Lihat Katalog</Button>
<Button variant="outline" className="rounded-full">Reset filter</Button>
```

### 5.2 Card

```tsx
<Card className="border bg-card text-card-foreground shadow-sm">
  <CardHeader>
    <CardTitle className="tracking-tight">Nama Produk</CardTitle>
    <CardDescription>Short description</CardDescription>
  </CardHeader>
</Card>
```

Di light: `bg-white` (`--card`). Di section premium full-bleed, card bisa `bg-muted` (`#D9FAF4`) agar kontras.

### 5.3 Badge

| Variant | Style |
|---------|-------|
| `default` | `bg-primary text-primary-foreground` — kategori aktif |
| `secondary` | `bg-secondary text-secondary-foreground` — child category |
| `outline` | `border border-border text-foreground` — filter inactive |
| `featured` (custom) | `bg-[#0F2A2A] text-[#00BFA6] tracking-widest uppercase text-[10px]` — untuk "Featured" / "Premium" |

### 5.4 Input / Select

- `border-input` (`#E0E0E0 tinta Aqua`) , `focus:ring-ring` (Teal), `bg-card`.
- Placeholder `text-muted-foreground/60`.

### 5.5 Layout Premium Blocks

Referensi memakai **split-screen editorial**. Replikasi di storefront:

```tsx
// Hero editorial 2-column
<section className="grid md:grid-cols-[1.1fr_1.9fr] min-h-[480px]">
  <div className="bg-[#0F2A2A] text-[#00BFA6] p-8 flex flex-col justify-between">
    <p className="text-xs tracking-[0.3em] uppercase opacity-80">A selection of</p>
    <h2 className="text-5xl font-bold tracking-tighter uppercase leading-none">DEEP<br/>LAGOON</h2>
    <span className="text-xs tracking-widest opacity-60">#0F2A2A</span>
  </div>
  <div className="grid grid-rows-2">
    <div className="bg-[#00BFA6] text-[#0F2A2A] p-8 flex flex-col items-center justify-center">
      <h2 className="text-4xl font-bold tracking-tighter uppercase text-center leading-none">TEAL<br/>WAVE</h2>
      <span className="text-xs tracking-widest mt-2">#00BFA6</span>
    </div>
    <div className="bg-[#D9FAF4] text-[#0F2A2A] p-8 flex flex-col items-center justify-center">
      <h2 className="text-4xl font-bold tracking-tighter uppercase text-center leading-none">AQUA<br/>SILK</h2>
      <span className="text-xs tracking-widest mt-2">#D9FAF4</span>
    </div>
  </div>
</section>
```

Untuk katalog: **filter bar** `bg-card border`, **product card** hover `hover:border-[#00BFA6]/40`.

### 5.6 Navbar / Footer

- `bg-[#0F2A2A] text-[#D9FAF4]` — selalu gelap (Deep Lagoon) agar premium, bukan putih.
- Link active: `text-[#00BFA6]`, hover: `hover:text-[#00BFA6]`.
- Cart badge: `bg-[#00BFA6] text-[#0F2A2A]`.

---

## 6. Ikonografi & Imagery

- Ikon: `lucide-react` (sudah via `components.json`).
- Stroke `1.5`, size `18-20px`, warna inherit `text-muted-foreground` atau `text-primary` untuk aksen.
- Image: optimization sudah via `ImageService` WebP 1200px. Tambah `border` tipis `border-border` pada `ProductCard` agar menyatu dengan palet terang.
- Empty state: gunakan `bg-muted` + icon `text-primary/40`.

---

## 7. Aksesibilitas (Kontras)

| Pasangan | Ratio | WCAG | Status |
|----------|-------|------|--------|
| `#0F2A2A` teks di `#D9FAF4` bg | 13.67:1 | AAA | ✅ Body utama light |
| `#0F2A2A` teks di `#00BFA6` bg | 6.50:1 | AA | ✅ Button primary |
| `#00BFA6` teks di `#0F2A2A` bg | 6.50:1 | AA | ✅ Link/heading di navbar |
| `white` teks di `#0F2A2A` bg | 15.16:1 | AAA | ✅ Heading di footer |
| `#00BFA6` teks di `white` bg | 2.33:1 | Fail | ❌ Jangan pakai |
| `#00BFA6` teks di `#D9FAF4` bg | 2.10:1 | Fail | ❌ Jangan pakai |

**Aturan praktis:**  
- Jika background terang (white / Aqua) → teks **Deep Lagoon** (`#0F2A2A`).  
- Jika background gelap (Deep Lagoon) → teks **Aqua Silk** / `white` / **Teal Wave** (untuk aksen besar ≥18px bold).  
- Teal hanya sebagai **background CTA** atau **border/accent**, bukan warna teks body.

Fokus: selalu `outline-none ring-2 ring-[#00BFA6] ring-offset-2`.

---

## 8. Implementasi — Cara Apply ke Velcommerce

### Langkah 1: Ganti `resources/css/app.css` variabel `:root` & `.dark`

Salin blok dari **§2.3** ke `resources/css/app.css` (ganti isi `:root` & `.dark` saja, biarkan `@theme` & `@layer base`). Pint shortcut:

```bash
# backup dulu
cp resources/css/app.css resources/css/app.css.bak
# lalu paste blok :root/.dark baru
```

### Langkah 2: Verifikasi

```bash
npm run build        # harus tanpa error
npm run dev          # cek di http://localhost:8000 - navbar Deep Lagoon, button Teal, background Aqua tint
vendor/bin/pint --dirty --format agent  # jika ubah PHP juga
```

### Langkah 3: Page-specific editorial block (opsional)

Untuk halaman `welcome` atau `products/index` hero, pakai split-screen di **§5.5** sebagai hero “Premium Collection”.

### Langkah 4: Dark mode toggle

Jika belum ada, pastikan `appearance` toggle memanggil `document.documentElement.classList.toggle('dark')` — warna akan otomatis switch ke Deep Lagoon background.

---

## 9. Token Cheat-Sheet (Tailwind)

```tsx
// Backgrounds
className="bg-background"        // light: off-white Aqua tint, dark: Deep Lagoon
className="bg-card"              // white / dark card
className="bg-primary"           // Teal Wave #00BFA6
className="bg-secondary"         // Aqua muted
className="bg-muted"             // Aqua Silk #D9FAF4
className="bg-[#0F2A2A]"         // Deep Lagoon arbitrary (untuk hero)
className="bg-[#00BFA6]"
className="bg-[#D9FAF4]"

// Text
className="text-foreground"      // Deep Lagoon / Aqua
className="text-primary"         // Teal Wave (hanya untuk large/bold di dark)
className="text-muted-foreground"
className="text-primary-foreground" // Deep Lagoon di atas Teal

// Border & Ring
className="border-border"
className="ring-ring"            // Teal

// Radius
className="rounded-lg"           // 0.625rem
className="rounded-full"         // pill @by.illarion
```

---

## 10. Contoh Penggunaan di Velcommerce

**ProductCard Featured Badge:**
```tsx
<Badge className="bg-[#0F2A2A] text-[#00BFA6] tracking-widest uppercase text-[10px] border-0">
  Premium
</Badge>
```

**Harga:**
```tsx
<span className="text-lg font-semibold tracking-tight tabular-nums text-foreground">
  {formatIDR(product.price)}
</span>
<span className="text-sm line-through text-muted-foreground">
  {formatIDR(product.compare_price)}
</span>
```

**Filter Active:**
```tsx
<Badge variant={category===cat.slug ? 'default' : 'outline'} // default = Teal bg
  className="cursor-pointer">
  {cat.name}
</Badge>
```

**Footer:**
```tsx
<footer className="bg-[#0F2A2A] text-[#D9FAF4] border-t border-white/10">
  <a className="hover:text-[#00BFA6] transition-colors">Bantuan</a>
</footer>
```

---

## 11. File Terkait

- `resources/css/app.css` — sumber token (ganti `:root`/`.dark`)
- `components.json` — `baseColor: neutral` tetap, tapi `cssVariables: true` jadi token dari `app.css` yang dipakai
- `resources/js/components/ui/button.tsx` — sudah pakai `bg-primary`, otomatis ikut Teal
- `vite.config.ts` — tidak perlu ubah

---

## 12. Referensi Visual

```
┌─────────────────┬──────────────────┐
│                 │   TEAL WAVE      │
│  DEEP           │   #00BFA6        │
│  LAGOON         │                  │
│  #0F2A2A        ├──────────────────┤
│  @by.illarion   │   AQUA SILK      │
│                 │   #D9FAF4        │
└─────────────────┴──────────────────┘
A selection of      premium styles
```

> Simpan file ini sebagai `docs/design-system.md`. Untuk handoff ke developer/designer, export palet ke Figma: buat 3 styles `Deep Lagoon / Teal Wave / Aqua Silk` + 9 semantic styles (`background`, `foreground`, `primary`, dst.) sesuai OKLCH di atas.

---

**Next step:** apply blok `:root`/`.dark` dari §2.3 ke `resources/css/app.css`, lalu `npm run build` dan screenshot `products/index` untuk verifikasi visual (light & dark).
