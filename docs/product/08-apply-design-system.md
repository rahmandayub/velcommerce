# 08 — Apply Teal Editorial Design System

> Wireframe grayscale → Visual dengan tokens dari DESIGN.md

## Mapping Tokens

| Wireframe Element | Token |
|-------------------|-------|
| Section Label "FEATURED MAKERS" | `label-editorial` (11px, 0.3em tracking, uppercase) |
| Headline "MADE BY HAND." | `display` (60px, tight -0.04em) — **uppercase only for hero** |
| Product title "Hand-thrown ceramic mug" | `headline-h2` (28px) — **sentence case** |
| Body copy | `body-main` (16px, 1.6) |
| Price | `price-tag` (18px, semibold) |
| Primary CTA "Explore Collection" | `primary: #006b5c` bg, `on-primary: #ffffff`, 8px radius, hover brightness 105% |
| Surfaces | `surface: #f4fbf7`, `surface-container: #e8efec` untuk card, `deep-lagoon: #0F2A2A` untuk footer/CTA block |
| Card | 10px radius, 1px `outline-variant: #bbcac5`, no shadow (flat minimal) |
| Image | 10px radius + 1px inner stroke di light mode |

## Revisi Rule Uppercase

**Sebelum:** "Display and H1 elements should always be uppercase."
**Sesudah:** "Display may use uppercase for editorial/marketing contexts. Product, transactional, and utility content must use sentence case."

Contoh:
- ✅ Hero: `MADE BY HAND.` (editorial, uppercase)
- ✅ PDP: `Hand-thrown ceramic mug` (product, sentence case)
- ✅ Order success: `Pesanan kamu dikonfirmasi` (transactional, sentence case)

## Page-Level Application

- **Home Hero:** 40/60 editorial split (image 40, text 60) di desktop, stack di mobile. Background `aqua-silk` atau image dengan overlay `deep-lagoon` 60%.
- **Explore by Craft:** Pill elements `rounded-full` untuk craft chips, border `deep-lagoon` 1px, hover `teal-wave`.
- **Featured Makers:** Card `surface-container-lowest: #ffffff` di atas `surface: #f4fbf7`, avatar 48px, label-editorial untuk location.
- **Product Card:** Image 10px, price-tag + maker byline (body-small, muted), availability badge 6px radius, low-saturation tint.

## Next: Prototype & Implement
- Prototype Figma dengan 5 halaman core (Home, Craft, PDP, Cart, Maker Store)
- Implement Laravel: migrasi makers + update products (maker_id, availability, materials, story, process_steps), seed 7 categories, build Home dengan Inertia.
