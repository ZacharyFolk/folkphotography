# FolkPhotography — Project Roadmap & Task Tracker

> Version this file in git. Check off tasks as you complete them. Move items between sections as priorities shift.

**Last updated:** 2026-03-03
**Status:** Pre-launch

---

## Launch Checklist — Do This Weekend

*Everything below unlocks the site. Estimated total: 3–4 hours.*

### Configuration (45 min)
- [ ] Create `Home` page in WordPress (blank body, publish)
- [ ] Create `Journal` page in WordPress (blank body, publish)
- [ ] Settings → Reading → set to "A static page", Homepage = `Home`, Posts page = `Journal`
- [ ] Create primary menu: Home, Gallery, Portfolio, Journal, Prints, About, Contact — assign to "Primary Menu" location
- [ ] Create `Hero Images` category (Posts → Categories)
- [ ] Create main photography categories: Astro, Wildlife, Street, Macro, Portraits, Travel, Film/Holga, Landscape
- [ ] Appearance → Customize → Hero Image Settings → select "Hero Images" category, set parallax speed

### Upload First Hero Images (20 min)
- [ ] Upload 5–10 best landscape-oriented images to Media Library
- [ ] Assign each to "Hero Images" category (edit attachment → Categories)
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
- [ ] Create `archive-portfolio.php` — renders Portfolio archive as grid

### Print Shop Improvements
- [ ] Add print size variations to products (5×7, 8×10, 11×14, 16×20, 24×36)
- [ ] Add paper type as variation (Matte, Lustre, Metallic, Fine Art Cotton)
- [ ] Add "Buy This Print" link from portfolio items to corresponding product pages

### Commission & Booking
- [ ] Install Contact Form 7 or WPForms — set up structured commission inquiry form (type of work, location, dates, budget, description)
- [ ] Embed Calendly (or equivalent) on Commission page for scheduling discovery calls
- [ ] Add "Book a Session" WooCommerce product (paid retainer)

### SEO & Social
- [ ] Install Yoast SEO or Rank Math (auto-generates sitemaps, OpenGraph, structured data for images)
- [ ] Add OpenGraph / Twitter Card meta tags so shared posts show featured image in preview
- [ ] Add image alt text and captions to key photos

### Performance
- [ ] Verify native lazy loading is working on all theme images (`loading="lazy"`)
- [ ] Install Imagify or ShortPixel for WebP image conversion and compression

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
