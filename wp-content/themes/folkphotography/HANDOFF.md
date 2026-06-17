# FolkPhotography — Project Handoff & New Session Prompt

I'm working on **FolkPhotography**, a custom WordPress photography portfolio system running locally on XAMPP at `C:\xampp\htdocs\localfolk\`. It has two components:

**Theme:** `wp-content/themes/folkphotography` — v1.1.5  
**Plugin:** `wp-content/plugins/i-was-here` — v0.6.0

### Theme Overview

Dark minimalist photography portfolio. Key characteristics:

- Custom post type: `portfolio` with taxonomies `portfolio_category`, `portfolio_tag`, `photo_subject`, `photo_style`
- Dark CSS variable system (`--black-rich`, `--grey-darkest`, etc.), fonts: Lato / Poppins / Rajdhani
- Fullscreen parallax hero on homepage, scrolled header behavior, GLightbox lightbox
- WooCommerce integration for print sales
- Three homepage widget areas: `homepage-after-hero`, `homepage-featured`, `homepage-gallery-section`
- Four registered custom widgets in `inc/widgets.php`: Recent Portfolio, Category Gallery, Photo Location Map, Camera & Photography Stats
- Masonry gallery page template at `page-templates/masonry-gallery.php` (CSS column-based masonry, JS repositioning on load/resize, category filtering via URL params)
- Leaflet maps for photo location display
- Version management: bump `style.css` header + `CHANGELOG.md` with every change

### Plugin Overview (i-was-here)

Auto-extracts EXIF/GPS from uploaded images, stores as post meta (`_iwh_*` keys).

- `includes/exif-reader.php` — reads raw EXIF, extracts camera data, converts GPS DMS→decimal
- `includes/attachment-hooks.php` — fires on `add_attachment`
- `includes/debug-tools.php` — bulk rescan tool (AJAX-based, 50 images/batch)
- `admin/meta-box-location.php` — manual lat/lng override in attachment editor
- `admin/tools-page.php` — consolidated admin tools page under Tools menu
- `admin/js/admin-map.js` — Leaflet + Nominatim geocoding (no API key required)
- `frontend/shortcode-world-map.php` — `[iwh_world_map]` shortcode, Leaflet, single optimized query
- Meta keys: `_iwh_lat`, `_iwh_lng`, `_iwh_place_name`, `_iwh_camera_make`, `_iwh_camera_model`, `_iwh_lens`, `_iwh_focal_length`, `_iwh_aperture`, `_iwh_shutter_speed`, `_iwh_iso`, `_iwh_date_taken`, `_iwh_has_exif`
- Version management: bump plugin header `Version:` + `IWH_VERSION` constant + `CHANGELOG.md`

### Dev Environment

- XAMPP local, WordPress at `C:\xampp\htdocs\localfolk\`
- File edits via Filesystem tools using precise str_replace (oldText/newText), never full file rewrites
- WordPress coding standards (proper hook usage, nonce verification, capability checks, sanitization)
- Grep for cross-file pattern searching before making changes

### Session Goals

[PASTE YOUR GOALS HERE]

---

---

## PROJECT STATE SNAPSHOT

| Component              | Version | Status              |
| ---------------------- | ------- | ------------------- |
| Theme: folkphotography | 1.1.5   | ✅ Production ready |
| Plugin: i-was-here     | 0.6.0   | ✅ Production ready |

### What's Working

- All four custom widgets registered and functional
- EXIF extraction on upload (camera make/model/lens/settings + GPS)
- Bulk rescan tool (consolidated admin page)
- Admin location meta box with Nominatim geocoding (no API key)
- `[iwh_world_map]` shortcode with Leaflet
- Masonry gallery page template with category filtering
- WooCommerce print shop integration
- GLightbox with EXIF data in description
- Homepage widget areas

### Known Limitations / Technical Debt

- Masonry layout uses JS absolute positioning (not CSS `columns` or a library like Masonry.js) — images must fully load before heights are correct
- Bulk rescan is synchronous per-batch with page reload between batches — no live progress bar
- Camera Stats widget runs direct SQL queries on every page load (no transient caching)
- Admin location meta box only shows when editing an attachment directly (`post.php`), not in Media Library modal
- No minified JS/CSS production builds
- Incomplete i18n (not all strings wrapped in translation functions)
- No PHPUnit tests

---

## NEW FEATURE TODO LIST

### 🎯 Priority 1 — New Gallery Blocks & Widgets (This Session)

#### 1.1 Masonry Gallery Block (Gutenberg Block)

Register a custom Gutenberg block `folkphotography/masonry-gallery` that:

- Lets the editor pick a portfolio category or hand-select images
- Renders a masonry layout on the frontend (consider switching to CSS `columns` approach for reliability, or use Masonry.js via CDN)
- Supports configurable column count (2/3/4) and gap size
- Integrates with GLightbox for lightbox on click
- Decision point: **theme block or plugin block?** Lean toward theme since it's presentation, but the plugin already has Leaflet asset management patterns to follow

#### 1.2 Random Category Photo Widget

New sidebar/homepage widget that:

- Pulls images from a selected portfolio category (or post category) randomly
- Can display as masonry, grid, or strip layout (layout selector in widget settings)
- Image source priority: (1) any images attached to/used in the post, (2) featured image fallback
- Pulling all images from post content requires parsing `<img>` tags or attachment relationships — consider `get_attached_media('image', $post_id)` as the most reliable approach
- Number of photos configurable (default: 6–12)
- Randomization: `orderby => 'rand'` in WP_Query, or pull a larger set and `array_rand()` in PHP
- Opens in GLightbox with EXIF data (consistent with existing lightbox integration)

#### 1.3 Masonry Layout CSS/JS Refactor (Supporting both above)

The current masonry in `page-templates/masonry-gallery.php` uses absolute JS positioning. Before building new masonry features, consider a unified approach:

- **Option A:** CSS `columns` (simplest, no JS, but column order not row order)
- **Option B:** CSS Grid with `grid-auto-rows` + `span` tricks (complex but true row-order masonry)
- **Option C:** Masonry.js library (battle-tested, ~15KB gzipped)
- Create a shared `assets/js/masonry-init.js` used by both the page template and any new widgets/blocks

---

### 🎯 Priority 2 — UX Improvements

#### 2.1 Image Lazy Loading

Add `loading="lazy"` to all gallery images in widgets, masonry template, and any new blocks. Pair with a CSS fade-in animation (`opacity: 0 → 1`) via IntersectionObserver for polish.

#### 2.2 Rescan Progress Feedback

The bulk rescan currently processes 50 images per page reload with no visual feedback. Upgrade to:

- AJAX endpoint for per-batch processing
- JavaScript progress bar in the admin tools page
- "X of Y images rescanned" counter
- Success/error count on completion

#### 2.3 Styled Empty States

Currently "No results" is plain text. Style empty states for:

- Masonry gallery (no posts in category)
- Location map widget (no GPS data yet)
- Category gallery widget (empty category)

---

### 🎯 Priority 3 — Technical Hardening

#### 3.1 Camera Stats Widget Caching

Wrap the 6 SQL queries in a transient (1-hour TTL). Bust the transient on `add_attachment` and `save_post`.

#### 3.2 Search Page Template

Site-wide AJAX search covering posts + portfolio items, styled dark to match theme. Triggered by a search icon in the header.

#### 3.3 Timeline / Photo Stream Widget

Chronological photo stream organized by month/year. Would pair with the existing stats and map widgets for a compelling "story of your photography" homepage section.

---

### 💡 Aspirational / Future Ideas

- **EXIF display on single posts** — Show camera data (make, model, focal length, aperture, ISO) in a styled panel below post content, auto-populated from `_iwh_*` meta
- **Location-based archive pages** — "All photos taken in [City/Country]" auto-generated from GPS data
- **Admin map in Media Library modal** — Currently the location meta box only shows in the full attachment editor. Adding it to the grid/list modal requires a custom AJAX panel (known WP limitation workaround)
- **Print-on-demand integration** — Currently WooCommerce is present but not deeply integrated with the portfolio. A "Buy Print" button on each portfolio item that auto-creates a WooCommerce product would be powerful
- **Advanced map features** — Clustering for the world map shortcode (Leaflet.markercluster), heatmap mode, date range filtering
- **Photo subject/style taxonomy archive pages** — Styled archive templates for the custom taxonomies already registered
- **Related photos widget** — On single portfolio posts, show other photos with matching camera, location, or subject
- **RSS/JSON feed** — Expose recent portfolio items + EXIF data as a structured feed for external use
- **Minified assets** — Create `.min.js` / `.min.css` production builds; could be a simple npm build step

---

## FILE STRUCTURE REFERENCE

NOTE: This is incorrect. Can you correct this to match the actual current structure?

```
themes/folkphotography/
├── style.css                    ← Theme header (version here)
├── functions.php                ← Enqueues, CPT registration, widget areas, includes
├── CHANGELOG.md
├── inc/
│   ├── widgets.php              ← All 4 custom widget classes
│   ├── post-types.php           ← Portfolio CPT + taxonomies
│   └── woocommerce.php          ← WooCommerce hooks/overrides
├── page-templates/
│   └── masonry-gallery.php      ← Masonry gallery page template
├── assets/
│   ├── js/main.js               ← Header scroll, parallax, mobile menu, GLightbox init
│   └── css/ (if split from style.css)
├── template-parts/
│   └── content-portfolio.php
├── woocommerce/                 ← WooCommerce template overrides
└── [8 documentation .md files]

plugins/i-was-here/
├── i-was-here.php               ← Plugin header (version + IWH_VERSION constant here)
├── CHANGELOG.md
├── includes/
│   ├── exif-reader.php          ← IWH_Exif_Reader class
│   ├── attachment-hooks.php     ← IWH_Attachment_Hooks class
│   ├── debug-tools.php          ← IWH_Debug_Tools class (bulk rescan)
│   └── logger.php               ← IWH_Logger class
├── admin/
│   ├── tools-page.php           ← Consolidated admin tools page
│   ├── meta-box-location.php    ← Attachment location meta box
│   └── js/admin-map.js          ← Leaflet + Nominatim for admin
└── frontend/
    ├── shortcode-world-map.php  ← [iwh_world_map] shortcode
    └── js/frontend-map.js       ← Leaflet init for frontend maps
```

---

## VERSION BUMP CHECKLIST

**Theme version bump:**

1. `style.css` — `Version: X.X.X`
2. `CHANGELOG.md` — Add entry

**Plugin version bump:**

1. `i-was-here.php` — `Version: X.X.X` in header comment
2. `i-was-here.php` — `define('IWH_VERSION', 'X.X.X');`
3. `CHANGELOG.md` — Add entry

---

## CODING CONVENTIONS

- Always check for existing scripts/styles before enqueueing (prevent duplicates, especially Leaflet)
- Nonce + capability check on all form submissions and AJAX handlers
- Sanitize inputs (`sanitize_text_field`, `absint`, `floatval`), escape outputs (`esc_html`, `esc_attr`, `esc_url`)
- Use `wp_add_inline_script()` to pass PHP data to JS (not `wp_localize_script` for non-string data)
- Custom post type slug: `portfolio`; main taxonomy: `portfolio_category`
- Meta key prefix: `_iwh_` for all plugin-stored data
- CSS follows existing variable system — never hardcode colors, always use `var(--xxx)`
