# FolkPhotography Theme - Changelog

## Version 1.3.0 - June 2026

### ✨ New Feature

**Portfolio Photo Meta Panel** (`single-portfolio.php`, `functions.php`, `style.css`)

New per-post toggle and unified photo meta layout on single portfolio items. Previously the EXIF panel was a simple list at the bottom of the page; description and EXIF were mutually exclusive. Now:

- **"Show photo description & camera data"** checkbox in the portfolio editor sidebar (side meta box). Defaults ON for all posts — unchecking hides the panel entirely.
- When enabled, a responsive two-column panel renders below the featured image: **left column** shows the media library image description; **right column** shows an EXIF grid.
- EXIF grid shows each value as a labelled cell (Camera, Lens, Focal Length, Aperture, Shutter, ISO, Date, Location). Each cell is only rendered if the data exists.
- Shutter speed is formatted from the raw fraction string (e.g. `130/10` → `13s`, `1/250` → `1/250s`).
- Date taken is formatted using the site's configured date format (`date_i18n`).
- Post content stacks below the panel independently — adding written content no longer suppresses the description or EXIF.
- Panel collapses to single column on mobile (≤768px). When no description is present, the EXIF grid spans the full width with auto-fill columns.
- Meta key: `_folk_show_photo_meta` on the portfolio post. Empty/absent = ON (backward compatible with existing posts).

---

### 🔧 Code Quality & Standards (this session)

**Escaped i18n throughout theme**

All `__()` calls in HTML contexts and all `_e()` calls converted to their escaped equivalents (`esc_html__()`, `esc_html_e()`, `esc_attr__()`). HTML entities in strings (e.g. `&rarr;`, `&rsquo;`) converted to Unicode equivalents to survive escaping. Files affected: `index.php`, `404.php`, `template-parts/content.php`, `inc/widgets.php`, `inc/media-admin.php`.

**IWH meta box strings internationalized** (`plugins/i-was-here/admin/meta-box-location.php`)

All hard-coded English strings in the plugin meta box wrapped in `__()` / `esc_html_e()` / `esc_attr__()` with text domain `i-was-here`.

---

### 🐛 Bug Fixes (this session)

**Wrong IWH meta keys throughout theme**

Templates and widgets were reading `_iwh_make`, `_iwh_model`, `_iwh_location_name` — none of which exist. The plugin writes `_iwh_camera_make`, `_iwh_camera_model`, `_iwh_place_name`. Camera display was always empty as a result. Also fixed to combine make + model with `trim($make . ' ' . $model)`. Fixed in: `single-portfolio.php`, `archive-portfolio.php`, `inc/widgets.php`, `inc/blocks.php`.

**Mixed-mode taxonomy filter using term IDs** (`page-templates/masonry-gallery.php`)

The `?cat=` URL parameter was passing a term ID into an OR tax_query across two taxonomies (`category` + `portfolio_category`). Term IDs are scoped per taxonomy — the same ID number may refer to different terms. Fixed by switching to slug-based filtering throughout: URL param now carries the slug, and every tax_query branch uses `'field' => 'slug'`. Filter button links and active-state checks also updated to use slugs.

**N+1 query in masonry gallery** (`page-templates/masonry-gallery.php`)

Each loop iteration called `get_post_thumbnail_id()` then individual `get_post_meta()` calls per field. Replaced with a single `get_post_meta($thumb_id)` call per item (returns all meta), then plucking values from the array with `$thumb_meta['_iwh_camera_model'][0] ?? ''`.

**Leaflet loaded unconditionally** (`functions.php`)

Leaflet CSS and JS were enqueued on every page load even when the I Was Here plugin was not active and no maps would ever render. Wrapped in `if ( defined( 'IWH_VERSION' ) )`.

---

### ✨ I Was Here Plugin — Optional Dependency

`functions.php` updated so the theme works fully standalone without the plugin:

- Leaflet only loads when `IWH_VERSION` is defined (plugin active).
- Admin notice shown on Dashboard, Themes, Upload, and Edit-Portfolio screens when plugin is not installed.
- All EXIF reads in templates use `get_post_meta()` directly — no coupling to plugin classes.

`README.md` updated to document this explicitly under "Optional: I Was Here Plugin".

---

### 🔧 EXIF Rescan Tool Rewrite (`plugins/i-was-here/admin/tools-page.php`)

The original `handle_rescan()` loaded all image attachments in a single HTTP request — a guaranteed timeout on any library with more than ~200 images.

Replaced with a batched AJAX approach:
- `BATCH_SIZE = 20` images per request
- Scan state (progress, total, offset, overwrite flag) persisted to `iwh_scan_state` WP option
- Resume button appears if a previous scan was interrupted
- Progress bar in the admin UI with live percentage
- `count_scannable()` uses `WP_Query::found_posts` with `posts_per_page: 1` for efficient COUNT
- Error handling per batch — a failed batch stops the scan and shows the error

---

## Version 1.2.1 - May 2026

### ✨ New Templates

**Portfolio Archive Template** (`archive-portfolio.php`)

Dedicated template for the `/portfolio/` URL — replaces the generic `index.php` fallback. Renders all portfolio items as a masonry grid with `portfolio_category` filtering via `?cat=` query param. Active category shows its name and description in the page header. Each item opens in GLightbox with EXIF data and a "View Full Portfolio Item" link.

**Portfolio Single Template** (`single-portfolio.php`)

Dedicated single-item template for `/portfolio/<slug>/`. Layout:
- Full-bleed featured image (outside the content wrapper, `hero-desktop` size)
- Title + category pill links
- Excerpt
- Post content / gallery (the actual images/body)
- EXIF panel (Camera, Lens, Focal Length, Exposure, Location) — only shown when data exists
- Portfolio tags in footer
- Prev/next navigation within the same `portfolio_category`

### 🎨 Styles

Added `#PORTFOLIO TEMPLATES` section to `style.css` covering `.portfolio-hero`, `.portfolio-header`, `.portfolio-categories`, `.portfolio-cat-link`, `.portfolio-excerpt`, `.portfolio-exif-panel`, `.exif-list`, `.exif-row`, `.portfolio-footer`, `.tag-link`, post-navigation overrides for single portfolio, and responsive breakpoints at 768px and 480px.

---

## Version 1.2.0 - May 2026

### ✨ New Features

**Masonry Gallery Gutenberg Block** (`folkphotography/masonry-gallery`)

New block available in the editor under the Media category. Lets you drop a responsive masonry photo grid anywhere in a post or page. Settings panel (InspectorControls) exposes:
- Source taxonomy: Portfolio Categories or Blog Categories
- Category: filter to a specific term
- Columns: 2 / 3 / 4
- Number of photos: 4–30

Server-side rendered; GLightbox opens each image with EXIF data in the description panel. Column count is responsive (tablet → 2, mobile → 1) regardless of the editor setting.

**New files:** `inc/blocks.php`, `js/block-masonry-gallery.js`

---

**Random Category Photos Widget**

New widget (`FolkPhoto_Random_Category_Widget`) in Appearance → Widgets. Pulls a randomized set of photos from a chosen category and renders them in one of three layouts:
- **Masonry** — CSS columns masonry (default)
- **Grid** — 3-column uniform grid  
- **Horizontal Strip** — scrollable single-row filmstrip

Settings: taxonomy (Portfolio or Blog), category term, layout, number of photos (2–20). Each image opens in GLightbox with EXIF data and a "View Full Post" link.

---

### 🔧 Refactor

**Masonry Layout — CSS Columns (1.3)**

Replaced the JS absolute-positioning masonry in the gallery template with pure CSS `column-count`. Removed the inline `<script>` block from `masonry-gallery.php` entirely. Layout now works immediately without waiting for image heights to resolve.

- `.masonry-grid` now uses `column-count: var(--masonry-cols, 3)` — blocks and widgets can pass `--masonry-cols` via inline style to override column count
- `.masonry-item` uses `break-inside: avoid` instead of `position: absolute`
- Responsive breakpoints: 2 columns at ≤1024px, 1 column at ≤480px
- Strip layout CSS added for the new widget

---

## Version 1.1.5 - March 2026

### 🐛 Bug Fixes

**GLightbox on Portfolio Widget Links**

The Recent Portfolio widget had `glightbox` class and `data-gallery` attributes on the permalink anchor. GLightbox was intercepting clicks and trying to open the WordPress page URL inside the lightbox instead of navigating to it. Removed the lightbox attributes — portfolio items now correctly link to their post pages.

**Fixed in:** `inc/widgets.php` — Recent Portfolio Widget

---

**Hero Image Fallback Never Ran**

`folkphotography_get_hero_image()` had an early `return false` when no hero category was configured, making the EXIF-based fallback query completely unreachable. Restructured the function so the fallback always runs when the category query returns nothing.

**Fixed in:** `functions.php` — `folkphotography_get_hero_image()`

---

**Location Map Widget — Empty Meta Rows Included**

The GPS meta query used `'compare' => 'EXISTS'` which matched rows with empty string values, potentially including attachments that had GPS keys saved with no value. Changed to `'compare' => '!='` with `'value' => ''` to require actual coordinates. Also added `'fields' => 'ids'` to avoid loading full post objects for potentially hundreds of markers.

**Fixed in:** `inc/widgets.php` — Location Map Widget

---

**Camera Stats Widget — Total Photo Count Undercounted**

`total_photos` was counting rows where `_iwh_camera_make != ''`, which excluded any photo with EXIF data but no identified camera make. Changed to count `_iwh_has_exif = '1'` joined to `wp_posts` restricted to `post_type = 'attachment'`.

**Fixed in:** `inc/widgets.php` — Camera Stats Widget

### ♿ Accessibility

**ARIA Attributes on Mobile Menu Toggle**

Added `aria-expanded="false"` and `aria-controls="primary-menu"` to the hamburger button in markup. JavaScript now updates `aria-expanded` to reflect actual open/closed state across all three menu event handlers (toggle, close-on-outside-click, close-on-item-click). Added `menu_id => primary-menu` to `wp_nav_menu()` so the `aria-controls` target exists in the DOM.

**Fixed in:** `header.php`, `js/main.js`

### 🌐 Internationalization

Wrapped hard-coded display strings in i18n functions across multiple files:

- `inc/widgets.php` — Camera Stats widget labels
- `page-templates/masonry-gallery.php` — filter group labels and type badges ("Show:", "All", "Portfolio", "Blog Posts", "Category:", "Blog")
- `single.php` — byline string `by %s` (also corrected to use `esc_html( get_the_author() )` instead of bare `the_author()`)

### 🔧 Code Quality

**functions.php Full Cleanup**

- Added section banners and docblocks to all functions
- Refactored `folkphotography_widgets_init()` to use a shared array for repeated arguments
- Expanded customizer descriptions
- Consistent formatting and indentation throughout
- Added `wp_reset_postdata()` call that was missing after the hero category query

---

## Version 1.1.4 - February 2026

### 🐛 Bug Fix

**JavaScript - Missing Null Guard in Menu Handler**

**Issue:** `handleMenuItemClick()` function checked `mainNav` but not `menuToggle` before using it. If `mainNav` existed but `menuToggle` was null, JavaScript would throw an error and break all subsequent JS on the page.

**Fixed in:** `js/main.js` (line 124)

**What Changed:**

```javascript
// BEFORE (BROKEN):
if (window.innerWidth <= 768 && mainNav) {
    mainNav.classList.remove('active')
    menuToggle.classList.remove('active') // ❌ menuToggle could be null!
}

// AFTER (FIXED):
if (!mainNav || !menuToggle) return // ✅ Guard both

if (window.innerWidth <= 768) {
    mainNav.classList.remove('active')
    menuToggle.classList.remove('active')
}
```

**Impact:**

- ✅ Consistent null checking across all menu functions
- ✅ No JavaScript errors on pages without header
- ✅ All JS continues to work properly

**Credit:** Code review feedback

---

## Version 1.1.3 - February 2026

### 🐛 Critical Bug Fix

**Masonry Gallery - Category Filter Broken for Portfolio**

**Issue:** The Masonry Gallery template used `cat` query parameter which only filters the built-in `category` taxonomy (for posts), but NOT `portfolio_category` (for portfolio CPT). In mixed views, the category filter didn't work correctly.

**Fixed in:** `page-templates/masonry-gallery.php`

**What Changed:**

- Portfolio-only view: Now uses `tax_query` with `portfolio_category`
- Posts-only view: Still uses `cat` (works as before)
- Mixed view: Uses `tax_query` with OR relation to match either taxonomy
- Filter UI: Now shows correct categories based on selected post type

**Impact:**

- ✅ Category filter works in portfolio-only view
- ✅ Category filter works in posts-only view
- ✅ Category filter works in mixed view (matches either taxonomy)
- ✅ Filter buttons show relevant categories for selected type

**Credit:** Code review feedback

---

## Version 1.1.2 - February 2026

### 🐛 Critical Bug Fix

**Camera Stats Widget - Wrong GPS Meta Keys**

**Issue:** The Camera Stats widget was querying `_iwh_latitude` and `_iwh_longitude` for the locations count, but the rest of the theme/plugin uses `_iwh_lat` and `_iwh_lng`. This caused the locations count to always show 0.

**Fixed in:** `inc/widgets.php` (lines 592-593)

- Changed: `_iwh_latitude` → `_iwh_lat`
- Changed: `_iwh_longitude` → `_iwh_lng`

**Impact:**

- ✅ Locations count now shows correct number
- ✅ Camera Stats widget fully functional
- ✅ Consistent meta keys throughout theme

**Credit:** Code review feedback

---

## Version 1.1.1 - February 2026

### 🔧 Bug Fixes

**Critical Issues from Code Review:**

1. **Fixed: Attachment Category Registration**
    - Added `register_taxonomy_for_object_type('category', 'attachment')`
    - Added `register_taxonomy_for_object_type('post_tag', 'attachment')`
    - Hero image category selector now works
    - Location Map widget category filter now works
    - Category Gallery widget now works
    - Users can now assign categories to images in Media Library

2. **Fixed: JavaScript Null Pointer Exception**
    - Added null guards to `closeMobileMenu()` function
    - Added null guards to `toggleMobileMenu()` function
    - Prevents errors when header markup is missing
    - Graceful degradation on all page types

3. **Improved: Code Quality**
    - All queries properly sanitized and escaped
    - Performance optimizations (passive event listeners)
    - Better error handling throughout

### 📝 Documentation

- **CODE-REVIEW-FIXES.md** - Complete documentation of all fixes

---

## Version 1.1.0 - February 2026

### ✨ New Features

#### **Widgets**

- **Photo Location Map Widget**: Interactive Leaflet.js map showing all photo locations from GPS data
    - Configurable height
    - Category filtering
    - Lightbox integration for map markers
    - Dark theme styled

- **Camera & Photography Stats Widget**: Auto-generated statistics dashboard
    - Total photos count
    - Most used camera/lens
    - Favorite settings (aperture, ISO)
    - Shooting locations count
    - Real-time updates as photos are uploaded

#### **Page Templates**

- **Masonry Gallery Template**: Pinterest-style portfolio layout
    - Staggered grid layout (3/2/1 columns responsive)
    - Built-in filtering (All/Portfolio/Posts, Categories)
    - Lightbox with EXIF data display
    - Hover overlays with post info
    - Fully responsive

### 🎨 Style Improvements

- Added Camera Stats Widget styling
- Added Location Map dark theme integration
- Added Masonry Gallery template styles
- Leaflet map controls dark theme
- Filter buttons with hover states

### 📦 Dependencies Added

- Leaflet.js 1.9.4 (CSS + JS) for interactive maps

### 📝 Documentation

- **NEW-FEATURES.md** - Complete guide to new features
- **WORKFLOW-GUIDE.md** - Daily productivity system & anti-overwhelm method ⭐
- **IMAGE-METADATA-GUIDE.md** - DxO/Photoshop EXIF preservation guide ⭐
- **DOCUMENTATION-INDEX.md** - Complete documentation overview ⭐
- Widget usage instructions
- Template customization guide
- Troubleshooting section

---

## Version 1.0.0 - January 2026

### Initial Release

- Fullscreen hero with parallax
- Dark minimalist design
- WooCommerce integration
- Portfolio custom post type
- Recent Portfolio widget
- Category Gallery widget
- GLightbox integration
- Custom taxonomies
- Responsive design
- i-was-here plugin integration

---

## Upgrade Notes

### From 1.0.0 to 1.1.0

**No breaking changes!** Simply update your theme files.

**New Files Added:**

- `/page-templates/masonry-gallery.php`
- `NEW-FEATURES.md`
- `WORKFLOW-GUIDE.md` ⭐
- `IMAGE-METADATA-GUIDE.md` ⭐
- `DOCUMENTATION-INDEX.md` ⭐
- `CHANGELOG.md` (this file)

**Modified Files:**

- `/inc/widgets.php` - Added 2 new widgets
- `/functions.php` - Added Leaflet library
- `/style.css` - Added widget and template styles

**What to Do After Update:**

1. Go to Appearance → Widgets to see new widgets
2. Create a page with "Masonry Gallery" template
3. Read NEW-FEATURES.md for usage instructions
4. Upload photos with GPS data for map widget

**Backward Compatible:**

- All existing widgets still work
- No changes to existing templates
- No database changes needed
- Safe to update on live sites
