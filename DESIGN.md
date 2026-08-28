---
name: Teal Editorial
colors:
    surface: '#f4fbf7'
    surface-dim: '#d4dcd8'
    surface-bright: '#f4fbf7'
    surface-container-lowest: '#ffffff'
    surface-container-low: '#eef5f2'
    surface-container: '#e8efec'
    surface-container-high: '#e3eae6'
    surface-container-highest: '#dde4e1'
    on-surface: '#161d1b'
    on-surface-variant: '#3c4a46'
    inverse-surface: '#2b3230'
    inverse-on-surface: '#ebf2ef'
    outline: '#6c7a76'
    outline-variant: '#bbcac5'
    surface-tint: '#006b5c'
    primary: '#006b5c'
    on-primary: '#ffffff'
    primary-container: '#00bfa6'
    on-primary-container: '#00473d'
    inverse-primary: '#44ddc2'
    secondary: '#496363'
    on-secondary: '#ffffff'
    secondary-container: '#cbe8e7'
    on-secondary-container: '#4f6969'
    tertiary: '#466460'
    on-tertiary: '#ffffff'
    tertiary-container: '#91b0ab'
    on-tertiary-container: '#264440'
    error: '#ba1a1a'
    on-error: '#ffffff'
    error-container: '#ffdad6'
    on-error-container: '#93000a'
    primary-fixed: '#68fade'
    primary-fixed-dim: '#44ddc2'
    on-primary-fixed: '#00201b'
    on-primary-fixed-variant: '#005045'
    secondary-fixed: '#cbe8e7'
    secondary-fixed-dim: '#b0cccb'
    on-secondary-fixed: '#031f1f'
    on-secondary-fixed-variant: '#314b4b'
    tertiary-fixed: '#c9e9e3'
    tertiary-fixed-dim: '#adcdc7'
    on-tertiary-fixed: '#01201d'
    on-tertiary-fixed-variant: '#2f4c48'
    background: '#f4fbf7'
    on-background: '#161d1b'
    surface-variant: '#dde4e1'
    deep-lagoon: '#0F2A2A'
    teal-wave: '#00BFA6'
    aqua-silk: '#D9FAF4'
    off-white: '#FAFEFE'
typography:
    display:
        fontFamily: Plus Jakarta Sans
        fontSize: 60px
        fontWeight: '700'
        lineHeight: '1.0'
        letterSpacing: -0.04em
    headline-h1:
        fontFamily: Plus Jakarta Sans
        fontSize: 36px
        fontWeight: '700'
        lineHeight: '1.1'
        letterSpacing: -0.03em
    headline-h2:
        fontFamily: Plus Jakarta Sans
        fontSize: 28px
        fontWeight: '600'
        lineHeight: '1.2'
        letterSpacing: -0.02em
    body-main:
        fontFamily: Plus Jakarta Sans
        fontSize: 16px
        fontWeight: '400'
        lineHeight: '1.6'
    body-small:
        fontFamily: Plus Jakarta Sans
        fontSize: 14px
        fontWeight: '400'
        lineHeight: '1.5'
    label-editorial:
        fontFamily: Plus Jakarta Sans
        fontSize: 11px
        fontWeight: '500'
        lineHeight: '1.4'
        letterSpacing: 0.3em
    price-tag:
        fontFamily: Plus Jakarta Sans
        fontSize: 18px
        fontWeight: '600'
        lineHeight: '1.2'
        letterSpacing: -0.01em
    display-mobile:
        fontFamily: Plus Jakarta Sans
        fontSize: 42px
        fontWeight: '700'
        lineHeight: '1.0'
        letterSpacing: -0.04em
rounded:
    sm: 0.25rem
    DEFAULT: 0.5rem
    md: 0.75rem
    lg: 1rem
    xl: 1.5rem
    full: 9999px
spacing:
    base: 4px
    unit-1: 0.25rem
    unit-2: 0.5rem
    unit-4: 1rem
    unit-6: 1.5rem
    unit-12: 3rem
    unit-20: 5rem
    gutter: 1.5rem
    margin-mobile: 1rem
    margin-desktop: 5rem
---

## Brand & Style

This design system embodies a premium, editorial commerce aesthetic, blending the precision of high-end fashion magazines with the functional clarity of modern minimalism. The visual narrative is built on "Editorial Contrast"—the juxtaposition of expansive whitespace, large-scale typography, and deep, monochromatic color blocks.

The brand personality is refined and confident, avoiding the cluttered "sales" look of traditional e-commerce in favor of a curated, boutique experience. It utilizes a **Minimalist** approach with a touch of **Corporate Modernity**, relying on structural grid alignment and sophisticated color theory (OKLCH) to convey luxury. The goal is to evoke a sense of calm ("tenang") and exclusivity, where the product is the hero and the interface acts as its frame.

## Colors

The palette is centered around a "Core 3" structure, utilizing **Deep Lagoon** for structural depth and high-contrast typography, **Teal Wave** for high-energy actions, and **Aqua Silk** for soft, secondary surfaces.

In **Light Mode**, the interface breathes through an off-white background with a subtle aqua tint, using Deep Lagoon for primary text to ensure maximum legibility. **Dark Mode** flips this hierarchy, utilizing Deep Lagoon as the primary canvas with Aqua Silk for secondary surfaces, creating a moody, cinematic environment.

We prioritize WCAG AA/AAA compliance by ensuring Teal Wave is reserved for graphical accents and CTAs, while text remains on high-contrast backgrounds. The use of OKLCH ensures that the perceived vibrance of the teals remains consistent across different device displays.

## Typography

The typography leverages the geometric clarity of **Plus Jakarta Sans** to maintain a contemporary, approachable feel within a luxury framework. The system is defined by two extremes: **tight tracking** for large headlines to create a compact, impactful look, and **extreme tracking (0.3em)** for small labels to evoke a high-fashion editorial feel.

Display and H1 elements should always be uppercase to reinforce the architectural nature of the design. For body text, we prioritize readability with a generous 1.6 line height. Pricing is treated as a distinct level, using semi-bold weights and tabular-num properties where possible to ensure alignment in product grids.

## Layout & Spacing

This design system uses a **Fluid Grid** approach with a strong emphasis on asymmetrical "Editorial Splits." On desktop, layouts often utilize a 12-column grid with a 24px (1.5rem) gutter, but key marketing sections should employ unconventional ratios (e.g., a 40/60 split) to maintain visual interest.

**Breakpoints:**

- **Mobile (< 768px):** 4-column grid, 16px margins, vertical stacking.
- **Tablet (768px - 1024px):** 8-column grid, 32px margins.
- **Desktop (> 1024px):** 12-column grid, 80px (5rem) margins for a "contained luxury" feel.

Spacing follows a strict 4px/8px rhythm. Section padding should be generous (80px on desktop) to allow the "Aqua Silk" and "Deep Lagoon" color blocks room to breathe, creating natural separation without the need for horizontal rules.

## Elevation & Depth

This system intentionally moves away from traditional Z-axis shadows, opting for a **Flat Minimal** philosophy that uses color-blocking and borders to define depth.

1.  **Tonal Layering:** Hierarchy is established by placing "Aqua Silk" surfaces on "Off-White" backgrounds. In dark mode, card surfaces are 4% lighter than the background.
2.  **Low-Contrast Outlines:** Instead of shadows, use 1px solid borders in an aqua-tinted shade for cards and inputs. This keeps the UI feeling light and integrated.
3.  **Selective Shadows:** Only use a very soft, highly diffused `shadow-sm` in light mode for floating elements like menus or cart drawers.
4.  **Focus States:** Use a 2px ring of "Teal Wave" with a 2px offset to ensure clarity without disrupting the flat aesthetic.

## Shapes

The shape language is defined by a consistent **0.625rem (10px)** radius, which softens the high-contrast color blocking and makes the premium palette feel more approachable.

- **Standard Cards & Inputs:** 10px radius (`rounded-md/lg`).
- **Secondary Buttons & Action Items:** Slightly tighter 8px radius.
- **Badges & Tags:** 6px radius for a sharper, more precise look.
- **Pill Elements:** Use `rounded-full` exclusively for global search bars or editorial "Category" tags to create a distinct visual contrast against rectangular product imagery.

## Components

### Buttons

- **Primary:** Solid "Teal Wave" background with "Deep Lagoon" text. No shadow, 8px radius. Hover state: `brightness(105%)`.
- **Secondary:** Transparent with a 1px "Deep Lagoon" border.
- **Editorial Pill:** Full-rounded buttons used for lifestyle CTAs, typically in "Deep Lagoon" with "Aqua Silk" text.

### Cards

- **Product Card:** No shadow. 10px rounded corners on both the image and the container. Use a 1px aqua-tinted border in light mode. Text is left-aligned with "Price Tag" typography.

### Input Fields

- **Text Inputs:** 10px radius, 1px border. Background should be "Aqua Silk" (Light Mode) or a slightly lighter "Deep Lagoon" (Dark Mode). Placeholder text uses "Body-Small" with reduced opacity.

### Chips & Badges

- **Status Badges:** 6px radius. Use "Label-Editorial" typography. Backgrounds should be low-saturation tints of the status color to maintain the "muted luxury" feel.

### Lists & Navigation

- **Navigation:** All-caps "Label-Editorial" typography with generous spacing. Hover states should use a simple color shift to "Teal Wave" or a subtle 1px underline.
- **Lists:** Use `tabular-nums` for any data-heavy lists or pricing tables to ensure mathematical alignment.

### Image Treatment

- All product photography must use the 10px radius. In light mode, apply a very thin 1px inner stroke to light-colored images to prevent them from bleeding into the background.
