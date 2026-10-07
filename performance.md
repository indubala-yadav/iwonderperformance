# Performance Recommendations for Web Magazine Theme

## 1) Reduce Unused CSS
- **Estimated savings:** 72 KB
- **Problem:** Three large, mostly‑unused stylesheets are loaded:
  - `bootstrap.min.css` (~32 KB, 92 % unused)
  - `all.min.css` (Font Awesome) (~23 KB, 99 % unused)
  - `layout.css` (~25 KB, 82 % unused)
- **Impact:** Render‑blocking CSS adds ~120 ms to FCP and LCP.
- **Fixes:**
  1. Enable **Optimize CSS Loading** in **Jetpack → Boost** – generates critical CSS and inlines above‑the‑fold styles.
  2. Enable **Concatenate CSS** in Jetpack Boost – merges CSS files into a single request.
  3. Audit Font Awesome usage – load only the icons you need or conditionally enqueue the library.

## 2) Reduce Unused JavaScript
- **Estimated savings:** ~40 ms on LCP
- **Problem:** Two third‑party scripts waste bandwidth and block rendering:
  - Google Tag Manager (`gtag/js`) – 177 KB total, 72 KB (~41 %) unused.
  - UserWay accessibility widget (`cdn.userway.org/...js`) – 47 KB total, 28 KB (~59 %) unused.
- **Impact:** External scripts cannot be optimized by WordPress.com CDN.
- **Fixes:**
  1. **Audit GTM tags** – remove unused tags and restrict firing to necessary pages. For a single GA4 property, replace GTM with Jetpack’s native Google Analytics snippet.
  2. **Defer or remove the UserWay widget** – enable lazy‑load in UserWay settings or uninstall if not needed.
  3. Enable **Defer Non‑Essential JavaScript** in Jetpack Boost.

## 3) Font Display (FOIT)
- **Estimated savings:** 130 ms
- **Problem:** Font Awesome fonts are loaded without `font-display`, causing Flash‑of‑Invisible‑Text.
  - `fa‑brands‑400.woff2` – 130 ms
  - `fa‑solid‑900.woff2` – 60 ms
  - `fa‑regular‑400.woff2` – 60 ms
- **Fix:** Add `font-display: swap;` to each `@font-face` rule, e.g.:
```css
@font-face {
  font-family: 'Font Awesome 6 Brands';
  font-style: normal;
  font-weight: 400;
  src: url('../webfonts/fa-brands-400.woff2') format('woff2');
  font-display: swap; /* added */
}
```
- **Implementation tip:** Edit the Font Awesome stylesheet in `wp-content/themes/web-magazine-theme/assets/fontawesome/` (e.g., `all.css`). Use a child theme to keep changes safe across updates.

## 4) Improve Image Delivery
- **Estimated savings:** 3,362 KB across 19 images
- **Problem:** Oversized images and missing modern formats (WebP/AVIF). Example worst offender: `7.Float_Sink_thumbnail.png` – 1.93 MB uploaded but displayed at 220 × 124 px.
- **Fixes:**
  1. **Enable Jetpack Site Accelerator (Image CDN)** – go to **Jetpack → Settings → Performance** and toggle *Enable site accelerator* and *Speed up image load times*.
  2. **Resize & recompress large images** before re‑uploading (e.g., replace the oversized PNGs with properly sized WebP versions).

---
*All steps assume Jetpack Boost is installed and active. Use a child theme for any theme file modifications to prevent loss on updates.*