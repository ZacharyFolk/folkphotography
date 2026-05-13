# FolkPhotography Theme - Changelog

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
