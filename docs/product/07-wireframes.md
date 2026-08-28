# 07 — Wireframes Grayscale: VelCommerce

> Aturan: grayscale dulu, tanpa warna Teal Editorial. Pertanyaan: "Apakah user tahu harus melakukan apa?"

## 07A — Home Wireframe

```
┌─────────────────────────────────────────────────────────────┐
│ [VelCommerce]  [Ceramics Textile Wood Jewelry Leather Home Art]  [Search] [Wishlist] [Cart] [User] │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  [Hero Image: artisan hands / studio]    MADE BY HAND.      │
│                                         MADE TO MATTER.     │
│                                         Discover unique     │
│                                         pieces from         │
│                                         independent         │
│                                         Indonesian makers.  │
│                                         [Explore Collection]│
├─────────────────────────────────────────────────────────────┤
│ EXPLORE BY CRAFT                                            │
│ [ Ceramics ] [ Textile ] [ Wood ] [ Jewelry ] [ Leather ] [ Home ] [ Art ] │
├─────────────────────────────────────────────────────────────┤
│ FEATURED MAKERS                          [View all makers →] │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ │
│ │ [Avatar]    │ │ [Avatar]    │ │ [Avatar]    │ │ [Avatar]    │ │
│ │ Mawar Studio│ │ Kana Weaves │ │ Kayu Rupa   │ │ Tanah Kita  │ │
│ │ Yogyakarta  │ │ Lombok      │ │ Jepara      │ │ Ubud        │ │
│ │ Ceramics    │ │ Textile     │ │ Woodcraft   │ │ Ceramics    │ │
│ └─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘ │
├─────────────────────────────────────────────────────────────┤
│ SELECTED PIECES — Selected pieces for your space    [View all →] │
│ [Product] [Product] [Product] [Product]                     │
│  maker byline, price, availability badge                    │
├─────────────────────────────────────────────────────────────┤
│ CRAFT STORY                                                 │
│ [Large image]  From raw clay to your table.                 │
│                How Mawar Studio shapes each piece...        │
│                [Read story →]                               │
├─────────────────────────────────────────────────────────────┤
│ NEW ARRIVALS / WEEKLY DROP                                  │
│ [Product] [Product] [Product] [Product]                     │
├─────────────────────────────────────────────────────────────┤
│ CTA BLOCK                                                   │
│ Discover the makers behind the things you love.             │
│ [Explore Makers] [Browse Collection]                        │
├─────────────────────────────────────────────────────────────┤
│ Footer: VelCommerce • Craft • Makers • Stories • Help       │
└─────────────────────────────────────────────────────────────┘
```

**Checks:** Hero tanpa flash sale, craft di atas product, maker sebagai section mandiri.

## 07B — Craft Listing (/craft/ceramics)

```
┌─────────────────────────────────────────────────────────────┐
│ Ceramics — Hand-shaped pieces from Indonesian studios       │
│ [Filter: Availability ▼] [Location ▼] [Material ▼] [Sort: Curated ▼] │
├─────────────────────────────────────────────────────────────┤
│ [Product] [Product] [Product] [Product]                     │
│ [Product] [Product] [Product] [Product]                     │
│ Pagination                                                  │
└─────────────────────────────────────────────────────────────┘
```

## 07C — Product Detail (/products/{slug})

```
┌─────────────────────────────────────────────────────────────┐
│ Breadcrumb: Home > Ceramics > Hand-thrown mug               │
├──────────────────────┬──────────────────────────────────────┤
│ [Gallery]            │ Hand-thrown ceramic mug              │
│ Main image 10px      │ By Mawar Studio — Yogyakarta [View Maker →] │
│ [Thumb][Thumb][Thumb]│ Hand-thrown stoneware, natural glaze │
│                      │ Rp185.000 • Ready Stock • 3 left     │
│                      │ [Add to Cart] [Wishlist]             │
│                      ├──────────────────────────────────────┤
│                      │ About this piece                     │
│                      │ Each piece is shaped...              │
│                      │ Materials: Stoneware clay, Natural glaze │
│                      │ Origin: Kasongan, Bantul             │
│                      │ Craft: Hand-thrown, Kiln fired       │
├──────────────────────┴──────────────────────────────────────┤
│ Made by Mawar Studio                                        │
│ [Avatar] Independent studio in Yogyakarta... [View Store →] │
├─────────────────────────────────────────────────────────────┤
│ Craft Process: Clay → Hand thrown → Drying → Glazing → Kiln fired → Finished │
├─────────────────────────────────────────────────────────────┤
│ Reviews (avg 4.8 • 12)  [Write review if purchased]         │
│ More from this Maker: [Product][Product][Product]           │
└─────────────────────────────────────────────────────────────┘
```

## 07D — Maker Store (/makers/{slug})

```
┌─────────────────────────────────────────────────────────────┐
│ [Cover image]                                               │
│ [Avatar] Mawar Studio — Yogyakarta • Ceramics               │
│ Story: Independent studio...                                │
│ [About →] [Location]                                        │
├─────────────────────────────────────────────────────────────┤
│ Products from Mawar Studio                                  │
│ [Product][Product][Product][Product]                        │
└─────────────────────────────────────────────────────────────┘
```

Next: `08-apply-design-system.md` — mapping wireframe ke Teal Editorial tokens.
