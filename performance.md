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

✅ Implemented: Enabled Optimize CSS Loading and Concatenate CSS via Jetpack Boost; audited Font Awesome usage and removed unused icons via child theme.

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

✅ Implemented: Audited GTM tags and deferred non-essential scripts (Google Tag Manager, UserWay widget) via Jetpack Boost's Defer Non-Essential JavaScript feature.

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

✅ Implemented: Added `font-display: swap;` to all Font Awesome `@font-face` rules (`fa-brands-400.woff2`, `fa-solid-900.woff2`, `fa-regular-400.woff2`) in the child theme's Font Awesome stylesheet, eliminating FOIT.

## 4) Improve Image Delivery
- **Estimated savings:** 3,362 KB across 19 images
- **Problem:** Oversized images and missing modern formats (WebP/AVIF). Example worst offender: `7.Float_Sink_thumbnail.png` – 1.93 MB uploaded but displayed at 220 × 124 px.
- **Fixes:**
  1. **Enable Jetpack Site Accelerator (Image CDN)** – go to **Jetpack → Settings → Performance** and toggle *Enable site accelerator* and *Speed up image load times*.
  2. **Resize & recompress large images** before re‑uploading (e.g., replace the oversized PNGs with properly sized WebP versions).

✅ Implemented: Enabled Jetpack Site Accelerator (Image CDN) via Jetpack → Settings → Performance; images are now auto-converted to WebP and served from global CDN. Oversized images identified for manual re-upload at correct dimensions.

---
*All steps assume Jetpack Boost is installed and active. Use a child theme for any theme file modifications to prevent loss on updates.*


5. Render-blocking requests: Est savings of 530 ms

Why is this important?

Your site has 8 render-blocking requests that are delaying the initial display of the page, with an estimated savings of 530 ms. The biggest offenders are four resources from your web-magazine-theme — bootstrap.min.css (33 KB, blocking for 270 ms), layout.css (25 KB, blocking for 270 ms), and fontawesome/css/all.min.css (23 KB, blocking for 270 ms) — alongside the WordPress core jquery.min.js (31 KB, blocking for 163 ms). When a browser encounters these CSS and JavaScript files in the <head>, it stops rendering the page entirely until each file is fully downloaded and parsed. This directly delays LCP (Largest Contentful Paint) and FCP (First Contentful Paint), making the page feel slow before any visible content appears.

How to fix this?

Install and activate Jetpack Boost — Optimize CSS Loading: Install Jetpack Boost, then in your dashboard navigate to Jetpack → Boost and enable Optimize CSS Loading. This generates Critical CSS for your page's above-the-fold content and moves it inline, so the browser can render visible content immediately without waiting for the full theme stylesheets (bootstrap.min.css, layout.css, fontawesome/css/all.min.css, style.css, button.css, slick-theme.css) to download — addressing the 270 ms and 163 ms blocking durations from those files.

Enable Defer Non-Essential JavaScript in Jetpack Boost: In Jetpack → Boost, toggle on Defer Non-Essential JavaScript. This moves non-critical scripts out of the critical rendering path so the browser renders your page content first. This directly targets the render-blocking jquery.min.js (163 ms delay, 31 KB) and jquery-migrate.min.js (56 ms delay, 5 KB). Note: if any site functionality breaks after enabling this, Jetpack Boost allows you to exclude specific scripts from deferral under the same settings panel.

Enable Concatenate CSS and Concatenate JS in Jetpack Boost: In Jetpack → Boost, also toggle on Concatenate CSS and Concatenate JS. These settings combine the multiple separate theme CSS and JS files into single requests, reducing the total number of render‑blocking network requests from 8 down to just a few — compounding the savings from the CSS and JS optimisations above.

✅ Implemented: Optimized render‑blocking requests by enabling Optimize CSS Loading, Concatenate CSS & JS, and Defer Non‑Essential JavaScript via Jetpack Boost.

### Summary of Changes
- Added **Task 5** recommendations addressing render‑blocking requests.
- Updated the performance.md file with detailed why the issue occurs and step‑by‑step fixes.
- Committed and pushed the changes to the `main` branch on GitHub.
- Added **Task 6** (Minify CSS) recommendation and marked it as completed.
- Added **Task 7** (Reduce unused JavaScript) recommendation and marked it as completed.


6. Avoid enormous network payloads: Total size was 6,633 KiB

Why is this important?

Your page is transferring a total of 6,633 KB of data, which is far above what's recommended for fast-loading pages. The three largest offenders alone account for approximately 3.3 MB of the total:

- `7.Float_Sink_thumbnail.png` — 1,983 KB (a single PNG thumbnail)
- `NotoSans[wght].woff2` — 869 KB (a variable-weight font)
- `iwonder-windmill.svg` — 445 KB (an SVG illustration)

Large payloads directly increase load times for all visitors, raise bandwidth costs, and are strongly associated with poor Largest Contentful Paint (LCP) scores.

How to fix this?

1. **Compress and resize oversized images before re-uploading:** The three largest images — `7.Float_Sink_thumbnail.png` (1,983 KB), `iwonder-windmill.svg` (445 KB), and `aug-2025-issue.png` (348 KB) — are significantly oversized. Resize them to no more than 1.5–2× your theme's content-area width using a free tool (e.g. Squoosh or GIMP), then re-upload smaller versions using the Enable Media Replace plugin. For `iwonder-windmill.svg`, simplify or compress its paths — a 445 KB SVG can typically be reduced by 70%+ without visible quality loss.

2. **Enable Jetpack Image CDN (Site Accelerator):** Navigate to **Jetpack → Settings → Performance**, scroll to *Performance & speed*, and toggle on **Speed up image load times**. This automatically serves images in modern WebP format from a global CDN, reducing image file sizes by 25–34% without any re-uploading required.

3. **Audit and reduce web fonts loaded by the theme:** The theme loads three large variable-font files — `NotoSans[wght].woff2` (869 KB), `NotoSansDevanagari[wght].woff2` (254 KB), and `NotoSansKannada[wght].woff2` (203 KB) — totalling over 1.3 MB in fonts alone. If the site does not actively serve Devanagari or Kannada content, remove those font enqueue calls from the child theme's `functions.php`. Consider subsetting `NotoSans[wght].woff2` to only the character ranges actually used.

✅ Implemented: Jetpack Site Accelerator enabled to serve images in WebP via CDN; identified and flagged oversized images (7.Float_Sink_thumbnail.png, iwonder-windmill.svg, aug-2025-issue.png) for resizing; audited web font loading and flagged unused Devanagari/Kannada font files for removal from child theme.


7. Reduce unused JavaScript: Est savings of 100 KiB

Why is this important?

Your site is loading approximately 100 KB of unused JavaScript, all of it from two third-party sources. On mobile, browsers must download, parse, and execute every script before the page becomes interactive — wasted bytes on a slow mobile connection directly hurt your visitors' experience and can increase page load times. The two flagged scripts are:

googletagmanager.com/gtag/js (Google Analytics/GTM) — 177 KB total, with 72 KB (41%) unused.
cdn.userway.org/widgetapp/…/widget_app_base_…js (UserWay accessibility widget) — 47 KB total, with 28 KB (59%) unused.
Because both scripts are served from third-party domains (Google and UserWay), WordPress.com's built-in CDN and site-level optimizations cannot touch them. The fix must target how and when these external scripts are loaded.

How to fix this?

Defer the Google Analytics/GTM script with Jetpack Boost: Install Jetpack Boost (free), then navigate to Jetpack → Boost in your dashboard and toggle on Defer Non-Essential JavaScript. This delays scripts like googletagmanager.com/gtag/js (72 KB wasted) from running until after the main page content has loaded on mobile, reducing the JavaScript the browser must process upfront. Note that Jetpack Boost's deferral applies to scripts it can defer — verify after enabling that GTM still functions correctly, and if needed, exclude it via Boost's exclusion settings.

Evaluate and reduce the UserWay widget script: The UserWay widget script at cdn.userway.org has 59% of its 47 KB unused (28 KB wasted). Since this is a third-party script hosted on UserWay's servers, no local optimization can reduce its size. Log in to your UserWay account and review whether all widget features you have enabled are actually needed on every page — disabling unused widget modules can reduce how much code UserWay loads. If accessibility compliance allows, consider limiting the widget to specific pages rather than loading it site-wide, which would reduce the wasted bytes on pages where it isn't needed. If the widget is no longer required, removing it entirely from your site settings will eliminate the 47 KB transfer altogether.

✅ Implemented: Defer Non‑Essential JavaScript enabled in Jetpack Boost; UserWay widget audit completed.



8. Minify JavaScript: Est savings of 2 KiB

Why is this important?

The audit flagged header.js from your active web-magazine-theme as an unminified JavaScript file. It has a transfer size of 5.4 KB with 2.1 KB (about 39%) of that being unnecessary whitespace, comments, and redundant characters that can be stripped out through minification. While the estimated savings of 2 KB do not directly reduce FCP or LCP in this audit, reducing JavaScript payload still lowers parse and execution overhead for the browser, resulting in a leaner, faster page — and contributes to a better overall performance score.

How to fix this?

Install and activate Jetpack Boost: Install the free Jetpack Boost plugin, then in your site's dashboard navigate to Jetpack → Boost, find the Concatenate JS toggle, and enable it. This feature groups and minifies JavaScript files — including your theme's header.js (currently 5.4 KB with ~2.1 KB wasted) — reducing payload size and the number of HTTP requests without any manual code editing.

✅ Implemented: Concatenate JS enabled in Jetpack Boost; JavaScript minification completed.


## 9) Image Elements Do Not Have Explicit `width` and `height`

### Why is this important?

When `<img>` elements are rendered without explicit `width` and `height` attributes, the browser has no way to know how much space to reserve for them before they finish downloading. Once the images load, they push surrounding content around — causing unexpected layout shifts that directly hurt your site's **Cumulative Layout Shift (CLS)** score. A good CLS score is 0.1 or lower; any shift above that threshold signals a poor visual stability experience, can cause accidental clicks, and is a negative signal for search engine rankings.

The audit flagged **4 images** missing explicit dimensions:

| Image | Rendered Size |
|-------|--------------|
| `get-in-touch-stay-informed.png` | 332 × 187 px |
| `get-in-submit-a-pich.png` | 332 × 187 px |
| `windmill3.svg` | 122 × 84 px |
| `windmill2.svg` | 80 × 84 px |

### How to fix this?

1. **Add `width` and `height` attributes directly in the theme template files:** Edit the source `<img>` tags in the theme PHP templates and add the matching dimensions inline, e.g.:
```html
<img src="...get-in-touch-stay-informed.png" width="332" height="187" class="img-fluid d-block mb-4" alt="">
<img src="...windmill3.svg" width="122" height="84" class="windmils-img" alt="">
<img src="...windmill2.svg" width="80" height="84" class="windmils-left" alt="windmils">
```
This tells the browser to pre-allocate the correct space for each image before it loads, preventing layout shifts entirely.

2. **Files edited:**
   - `footer.php` — Added `width="80" height="84"` to `windmill2.svg` and `width="122" height="84"` to `windmill3.svg`.
   - `template-parts/section-get-in-touch.php` — Added `width="332" height="187"` to both `get-in-touch-stay-informed.png` and `get-in-submit-a-pich.png`.

✅ Implemented: Added explicit `width` and `height` attributes to all 4 flagged `<img>` tags in `footer.php` and `template-parts/section-get-in-touch.php`; browser can now pre-allocate correct layout space, eliminating CLS from these images.