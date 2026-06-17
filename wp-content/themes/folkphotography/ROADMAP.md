# FolkPhotography — Project Roadmap & Task Tracker

> Version this file in git. Check off tasks as you complete them. Move items between sections as priorities shift.

**Last updated:** 2026-05-13
**Status:** Pre-launch

---

## Launch Checklist — Do This Weekend

*Everything below unlocks the site. Estimated total: 3–4 hours.*

### Configuration (45 min)
- [ ] Create `Home` page in WordPress (blank body, publish)
- [ ] Create `Journal` page in WordPress (blank body, publish)
- [ ] Settings → Reading → set to "A static page", Homepage = `Home`, Posts page = `Journal`
- [ ] Create primary menu: Home, Portfolio, Journal, Prints, About, Contact — assign to "Primary Menu" location
- [ ] Create main photography categories: Astro, Wildlife, Street, Macro, Portraits, Travel, Film/Holga, Landscape
- [ ] Appearance → Customize → Hero Image Settings → set parallax speed

### Upload First Hero Images (20 min)
- [ ] Upload 5–10 best landscape-oriented images to Media Library
- [ ] In Media Library (list view), open each image and check **"Use in homepage hero rotation"** — no category needed
- [ ] Verify hero is showing on homepage

### Homepage Widgets (20 min)
- [ ] Appearance → Widgets → Homepage After Hero: add **Recent Portfolio Items** (Title: "Latest Work", 6 items, 3 columns)
- [ ] Appearance → Widgets → Homepage Featured: add **Camera & Photography Stats** (Title: "By The Numbers")
- [ ] Appearance → Widgets → Homepage Gallery Grid: add **Category Gallery** (pick a category, 9 items, 3 columns)

### Quick Pages (30 min)
- [ ] Create "Gallery" page → assign **Masonry Gallery** page template (already built, just needs a page)
- [ ] Create "Where I've Been" page → add `[iwh_world_map]` shortcode to body
- [ ] Create "About" page — write it like a human, include a photo of yourself
- [ ] Create "Commission" page — what you shoot for hire, contact form or email link

### Print Shop (1–2 hrs)
- [ ] Add first 3–5 print products to WooCommerce (photo, title, description, price)
- [ ] Verify shop page, cart, and checkout are working
- [ ] Add at least one digital download product

---

## Phase 1 — Before Launch is "Real" (High Impact, Low Effort)

### Single Post / Photo Essay
- [ ] Add EXIF data display to `single.php` — show camera, lens, aperture, ISO, date taken below photo
- [ ] Add "Related Posts" by category at bottom of each post
- [ ] Make featured image full-width on `single.php` (currently constrained inside content wrapper)

### Archive & Discovery
- [ ] Create `category.php` template — renders photo grid instead of text list when visiting a category archive
- [x] Create `archive-portfolio.php` — renders Portfolio archive as grid — 2026-05
- [x] Create `single-portfolio.php` — full-bleed hero, EXIF panel, category pills, prev/next nav — 2026-05

### Print Shop Improvements
- [ ] Add print size variations to products (5×7, 8×10, 11×14, 16×20, 24×36)
- [ ] Add paper type as variation (Matte, Lustre, Metallic, Fine Art Cotton)
- [ ] Add "Buy This Print" link from portfolio items to corresponding product pages

### Commission & Booking
- [ ] Install Contact Form 7 or WPForms — set up structured commission inquiry form (type of work, location, dates, budget, description)
- [ ] Embed Calendly (or equivalent) on Commission page for scheduling discovery calls
- [ ] Add "Book a Session" WooCommerce product (paid retainer)

### SEO & Social
- [ ] Install Rank Math (free) — handles meta descriptions, OpenGraph, Twitter Cards, JSON-LD schema, XML sitemap, and Search Console integration in one plugin
- [ ] Add image alt text and captions to key photos in Media Library

### Performance
- [ ] Install Imagify or ShortPixel for WebP image conversion and compression

---

## Performance & SEO Sprint — Lighthouse 100 Target

*Findings from a full code audit (2026-05-13). Ordered by impact. Goal: 100 on Lighthouse Performance + strong search visibility. No new features — pure optimization.*

---

### Tier 1 — Core Web Vitals (Do These First)

- [ ] **Convert hero from CSS background-image to `<img>` tag** — biggest single LCP win. Current `background-image` approach means the browser can't prioritize it, Google Images can't index it, and `loading="eager"` / `fetchpriority="high"` can't be applied. Switch to a real `<img>` with `loading="eager"` and `fetchpriority="high"`. Add `<link rel="preload" as="image">` in `wp_head` for the front page only. Also enables responsive `srcset` so phones stop downloading a 2560px image. *(front-page.php, functions.php)*

- [ ] **Fix N+1 EXIF meta queries** — masonry gallery page fires 150+ extra DB queries per load (5× `get_post_meta()` per post, 30 posts). Same pattern in `widgets.php`, `blocks.php`, and `archive-portfolio.php`. Fix: call `get_post_meta($id)` once with no key (returns all meta as an array), then read individual keys from that array. Zero extra queries. *(inc/widgets.php, inc/blocks.php, archive-portfolio.php, page-templates/masonry-gallery.php)*

- [ ] **Transient caching on all five widgets** — every widget re-queries the database on every page load. Camera Stats widget fires 7 raw `$wpdb` queries each time; Location Map fetches up to 500 attachments. Wrap each widget's `widget()` output in `get_transient()` / `set_transient()` with a 12–24 hr TTL. Clear cache on `save_post` and `delete_post` hooks. *(inc/widgets.php)*

- [ ] **Self-host Google Fonts** — Google Fonts is render-blocking CSS loaded from an external CDN (DNS lookup + connection + TLS on every visit). Download the WOFF2 files for Lato, Poppins, and Rajdhani, serve locally with `@font-face`, add `font-display: swap`. Removes a render-blocking external request entirely. *(functions.php, style.css)*

- [ ] **Conditional enqueue for GLightbox and Leaflet** — both libraries load on every page (single posts, shop, 404) even when there's no lightbox or map present. GLightbox: only enqueue when a page contains `.glightbox` elements (set a flag in templates that use it). Leaflet: only enqueue when the Location Map widget is active in the current sidebar. *(functions.php)*

- [ ] **Add `loading="lazy"` to all below-fold images** — none of the masonry grids, widget thumbnails, or archive images have explicit lazy loading. Add `add_theme_support('lazy-load-images')` in `functions.php`. For images confirmed above the fold (hero, first post thumbnail on journal), explicitly pass `array('loading' => 'eager')` to override. *(functions.php, all templates)*

- [ ] **Add explicit width/height to all post thumbnails** — missing dimensions cause CLS (layout shift) as images load. WordPress 5.5+ outputs these automatically if you use registered size names in `the_post_thumbnail()` — verify this is working. For the hero `<img>` (once converted), hardcode `width="1920" height="1080"`. *(all templates)*

- [ ] **Fix `orderby=rand` in Random Category Widget** — `ORDER BY RAND()` forces a full table scan and bypasses MySQL query cache; gets slower as post count grows. Alternative: fetch a wider set ordered by ID and shuffle in PHP, or use a random `OFFSET` calculated from total count. *(inc/widgets.php)*

---

### Tier 2 — SEO Fundamentals

- [ ] **Install Rank Math SEO (free)** — single plugin covers: meta descriptions, OpenGraph, Twitter Cards, JSON-LD schema (Article, ImageObject, BreadcrumbList, WebSite, Person), XML sitemap, Google Search Console integration. Gets the most impactful SEO items done without writing the meta tag code manually. *(plugin)*

- [ ] **Verify XML sitemap is active** — WordPress 5.5+ auto-generates `/wp-sitemap.xml` covering posts, pages, and custom post types. Confirm it works, then submit to Google Search Console.

- [ ] **Add JSON-LD schema markup in theme** (if not using Rank Math, or to supplement it):
  - `WebSite` schema on every page (enables Google Sitelinks Search Box)
  - `Person` / `ProfilePage` schema (photographer name, bio, social links)
  - `Article` schema on blog posts (headline, datePublished, dateModified, author, image)
  - `ImageObject` schema on single portfolio items (contentUrl, description, author, camera EXIF)
  - `BreadcrumbList` on category archives and single posts

- [ ] **Add `<time datetime="...">` to all post dates** — dates are currently plain text strings; `<time datetime="2026-05-13">` makes them machine-readable for schema parsers and screen readers. Replace `get_the_date()` with `<time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>`. *(single.php, template-parts/content.php)*

---

### Tier 3 — Asset Delivery & Rendering

- [ ] **Self-host Leaflet and GLightbox** — currently loaded from `unpkg.com` and `jsdelivr.net` respectively. Download both libraries into `/js/vendor/` and `/css/vendor/` and enqueue from the theme. Removes two external CDN connections, improves reliability, and puts all assets under your browser cache headers. *(functions.php)*

- [ ] **Guard the hero-count WP_Query in Customizer** — `folkphotography_customizer()` runs a `WP_Query` to count hero images on every page load, not just when the Customizer is open. Wrap it in `if ( is_customize_preview() )` so it only fires when actually needed. *(functions.php)*

- [ ] **Add `<link rel="preload">` hint for hero image** — even after converting the hero to an `<img>`, the browser discovers it after parsing the full HTML. Output a preload link for the hero image URL via `wp_head` on the front page only. Starts the image fetch at the earliest possible moment. *(functions.php)*

- [ ] **Add `decoding="async"` to non-hero images** — tells the browser to decode images off the main thread, allowing rendering to continue without blocking. Add to all `the_post_thumbnail()` calls that aren't the LCP image: `array('decoding' => 'async', 'loading' => 'lazy')`. *(all templates)*

- [ ] **Hero responsive srcset** — once converted to `<img>`, the hero always serves the 2560px `hero-fullscreen` image even on mobile. Set up `srcset` with `hero-fullscreen` (2560px) and `hero-desktop` (1920px), and a `sizes` attribute so the browser picks the appropriate size. *(front-page.php)*

- [ ] **Optimize Camera Stats widget: 7 queries → 1–2** — the widget runs 7 separate `$wpdb->get_var()` calls for camera, lens, ISO, aperture, and location stats. These can be reduced to 1–2 queries using `GROUP BY` and `ORDER BY COUNT(*) DESC LIMIT 1` patterns in a single pass. *(inc/widgets.php)*

- [ ] **Location Map Widget: meta_query pre-filter** — the widget fetches up to 500 attachment IDs then loops calling `get_post_meta()` per image to check for GPS coordinates. Move the GPS filter into the `WP_Query` itself using `meta_query` so only attachments that actually have lat/lng are returned. *(inc/widgets.php)*

---

### Tier 4 — HTML Quality & Small Best Practice

- [ ] **Fix `h3` → `h2` in masonry overlay titles** — `archive-portfolio.php` uses `<h3 class="masonry-title">` directly under `<article>`, skipping h2. Heading hierarchy should be h1 (page title) → h2 (item title). *(archive-portfolio.php line 94)*

- [ ] **Improve 404.php** — current 404 just says "Nothing Found" with no navigation. Add a search form (`get_search_form()`), links to Portfolio and Journal, and 4–5 category links. Reduces bounce rate and keeps users on the site. *(404.php)*

- [ ] **Replace `_e()` with `esc_html_e()`** — a few places in `index.php` and `single.php` use `_e()` for output. WordPress coding standards require the escaped version. No XSS risk on translation strings, but it's flagged by code audits and PHPCS. *(index.php, single.php)*

- [ ] **Cache `get_post_thumbnail_id()` in loops** — `masonry-gallery.php` calls `get_post_thumbnail_id()` multiple times per iteration. Assign to a `$thumb_id` variable once per loop pass and reuse it. *(page-templates/masonry-gallery.php)*

- [ ] **Footer copyright year: use `date('Y')` instead of hardcoded year** — minor but becomes incorrect automatically. *(footer.php)*

- [ ] **Add `rel="noopener noreferrer"` to any external links** — prevents opened tabs from accessing `window.opener`, minor security best practice.

---

## Phase 2 — Makes the Site Special (Medium Effort)

### Signature Features
- [ ] **Timeline page template** — photos displayed chronologically using `_iwh_date_taken` EXIF meta, grouped by Year → Month. Auto-populates as you upload. This is the feature that makes a photography site feel like a living archive.
- [ ] **"Gear" page** — auto-generated from EXIF stats: cameras used, lenses used, total shots per camera, most-used settings. Pulls from the Camera Stats widget data.
- [ ] Add `photo_subject` taxonomy values to posts (people, architecture, nature, birds, urban, etc.)
- [ ] Add `photo_style` taxonomy values to posts (long-exposure, black-and-white, film, composite, etc.)
- [ ] Build taxonomy archive template for `photo_subject` and `photo_style` (filterable photo grid)

### Taxonomy Work
- [ ] Audit all existing photos — assign categories and featured images
- [ ] Create first 3 Portfolio items (curated collections: one per main subject)
- [ ] Assign `portfolio_category` to portfolio items

### Performance (Technical Debt)
- [ ] Add transient caching to widget queries (cache for 12–24 hrs, clear on new post)
- [ ] Add Leaflet.markercluster to map widget (clusters nearby markers instead of pile-up)
- [ ] Add AJAX progress bar to EXIF rescan tool in i-was-here plugin

### Growth
- [ ] Set up email newsletter signup (Mailchimp, ConvertKit, or similar) — embed on homepage and About page
- [ ] Add social sharing to single posts (Web Share API for mobile, copy-link fallback)
- [ ] Add post navigation thumbnails (show featured image for prev/next instead of just title text)

### Private Client Galleries
- [ ] Set up password-protected pages for commissioned clients to view/download their photos
- [ ] Create workflow: shoot → upload to private page → share password with client

---

## Phase 3 — Roadmap / Dream Features (Higher Effort)

- [ ] **Year in Review template** — auto-generates annual recap from EXIF dates: top categories, cameras used, map of places visited, total shots, first and last photo of the year
- [ ] **EXIF-based search** — filter photos by camera, lens, focal length, date range, aperture
- [ ] **Reverse geocoding** — GPS coordinates → readable place names on map popups
- [ ] **Darkroom / Film section** — dedicated area for film photography: developing notes, contact sheets, technique essays. Very niche, very personal, exactly the thing that builds a loyal audience.
- [ ] **Photo comparison slider** — before/after for RAW vs edit, or digital vs film of the same scene
- [ ] **Print lab integration** — Prodigi or WHCC API for automatic order fulfillment (customer buys, lab prints and ships directly, no inventory)
- [ ] **Commercial license calculator** — simple wizard for editorial/commercial buyers ("what license do I need and what does it cost")
- [ ] **PWA / installable app** — add web manifest so mobile visitors can "install" the site
- [ ] **AJAX infinite scroll** on archives (replace pagination with scroll-to-load)
- [ ] **Instagram feed widget** — show recent posts in footer/sidebar

---

## Ideas Parking Lot

*Things to think about, not committed to yet.*

- RSS feeds per category (photographers can subscribe to just Astro, just Wildlife, etc.)
- Equipment reviews/notes (write about specific cameras and lenses you use)
- Reading time displayed on photo essays
- Film photography workflow notes (developer, stop, fix, times, temperatures)
- Collections feature for "Best of [Year]" curation

---

## Completed

*Move items here when done. Keep the date.*

- [x] Dark minimalist theme built and styled (custom) — 2025
- [x] Automatic EXIF/GPS extraction via i-was-here plugin — 2025
- [x] Hero section with parallax scrolling — 2025
- [x] Portfolio custom post type — 2025
- [x] WooCommerce integration (shop, cart, checkout dark-themed) — 2025
- [x] GLightbox lightbox across all galleries — 2025
- [x] Location map widget (Leaflet, GPS-tagged photos) — 2025
- [x] Camera stats widget (auto from EXIF) — 2025
- [x] Masonry gallery page template with dual filtering — 2025
- [x] Mobile responsive menu — 2025
- [x] Homepage fixed — documented Settings → Reading setup correctly — 2026-03-03
- [x] GETTING-STARTED.md rewritten with explicit homepage setup instructions — 2026-03-03
- [x] ROADMAP.md created — 2026-03-03
- [x] Hero image system refactored — per-image checkbox in Media Library replaces category-based selection; Customizer panel shows live count — 2026-05
- [x] Media Library admin enhancements — filter options, hero toggle in grid view, thumbnail column in post list — 2026-05
- [x] CSS columns masonry refactor — replaced JS absolute-positioning with pure CSS `column-count`; inline script removed from template — 2026-05
- [x] Masonry Gallery Gutenberg block (`folkphotography/masonry-gallery`) — category picker, columns 2–4, count 4–30, server-side rendered, GLightbox + EXIF — 2026-05
- [x] Random Category Photos widget — masonry / grid / strip layouts, randomized via `orderby: rand`, GLightbox + EXIF in lightbox description — 2026-05
