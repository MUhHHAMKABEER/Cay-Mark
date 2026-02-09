# Homepage Image Dimensions Reference

This document lists **all image areas** on the homepage view (`resources/views/welcome.blade.php`) and the layout (`resources/views/layouts/welcome.blade.php`), with recommended dimensions for assets.

---

## 1. Hero banner (carousel slides)

| Location | File(s) | Display size | Recommended image dimensions |
|----------|---------|--------------|------------------------------|
| Hero section (top of page) | `images/banner-1.jpg`, `images/banner-2.jpg`, `images/banner-3.jpg` | **Full width × 600px** (section has `h-[600px]`) | **1920 × 600** px (or 1600 × 600). Use landscape; `bg-cover bg-center` crops to fill. |

- **CSS:** `bg-cover bg-center` — image fills the block and is centered.
- **Aspect ratio:** ~3.2:1 (wide landscape).

---

## 2. Popular Car Auctions (listing thumbnails)

| Location | Source | Display size | Recommended dimensions |
|----------|--------|--------------|------------------------|
| Each auction card image | First listing image or `images/placeholder-car.png` | **Full card width × 224px** (`h-56` = 14rem) | **600 × 336** px (2:1 or 16:9). Min **400 × 224** px. |

- **CSS:** `w-full h-56 object-cover` — height fixed at **224px**, width scales with card (e.g. ~300px on large screens, 4 columns).
- **Placeholder:** Use same aspect ratio as above when no listing image.

---

## 3. Vehicle Finder / Auction Car Finder (marketplace grid)

| Location | Source | Display size | Recommended dimensions |
|----------|--------|--------------|------------------------|
| Each vehicle card image | First listing image or `images/placeholder-car.png` | **Full card width × 192px** (`h-48` = 12rem) | **600 × 288** px (or 2:1). Min **400 × 192** px. |

- **CSS:** `w-full h-48 object-cover` — height fixed at **192px**, width scales with card (3–4 columns).
- **Placeholder:** Same aspect ratio when no image.

---

## 4. “Why Use CayMark?” highlight cards (4 images)

| Location | File(s) | Display size | Recommended dimensions |
|----------|---------|--------------|------------------------|
| Highlight 1 | `images/highlight-1.jpg` | **Full card width × 224px** | **600 × 336** px (or 400 × 224). |
| Highlight 2 | `images/highlight-2.jpg` | **Full card width × 224px** | **600 × 336** px. |
| Highlight 3 | `images/highlight-3.jpg` | **Full card width × 224px** | **600 × 336** px. |
| Highlight 4 | `images/highlight-4.jpg` | **Full card width × 224px** | **600 × 336** px. |

- **CSS:** `w-full h-56 object-cover` — height **224px**, width ~25% of container on large screens (4 columns).

---

## 5. Logo (site-wide)

| Location | File | Display size | Recommended dimensions |
|----------|------|--------------|------------------------|
| Header (unified-header) | `Logos/1.png` (config: `logos.header`) | **Height 40–48px** (`h-10`–`h-12`), width auto | **Height 80px** (or 96px for retina); width proportional. |
| Footer | `Logos/2.png` (config: `logos.footer`) | **Height 48px** (`h-12`), width auto | Same as above. |
| Sidebar (buyer/seller/admin) | `Logos/1.png` (config: `logos.sidebar`) | **Height 40px** (`h-10`), width auto | Same as above. |
| Invoice PDF | `Logos/1.png` (config: `logos.invoice`) | **Max height 48px** | Same as above. |

- **Path:** All logo files live in **`public/Logos/`** (e.g. `1.png`, `2.png`, … `12.png`). Which file is used where is set in **`config/logos.php`**.

---

## Summary table

| Image area | Display width | Display height | Recommended size (W × H) |
|------------|---------------|----------------|---------------------------|
| Hero banners (3) | 100% viewport | 600px | **1920 × 600** |
| Popular auctions (listing/placeholder) | Variable (~300px/card) | 224px | **600 × 336** (or 400 × 224) |
| Vehicle finder (listing/placeholder) | Variable (~300px/card) | 192px | **600 × 288** (or 400 × 192) |
| Highlight 1–4 | Variable (~300px/card) | 224px | **600 × 336** (or 400 × 224) |
| Logo (legacy) | Auto | 80px | **× 80** (e.g. 200 × 80) |

---

## File paths (for reference)

- Hero: `public/images/banner-1.jpg`, `banner-2.jpg`, `banner-3.jpg`
- Placeholder: `public/images/placeholder-car.png`
- Highlights: `public/images/highlight-1.jpg` … `highlight-4.jpg`
- Logos: `public/Logos/1.png`, `2.png`, … (see `config/logos.php`)

All dimensions assume `object-cover` or `bg-cover`: images are cropped to fill the area while keeping aspect ratio; providing the recommended size (or larger) avoids upscaling and keeps quality good on retina.
