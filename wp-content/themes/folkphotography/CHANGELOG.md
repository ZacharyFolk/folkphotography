# FolkPhotography Theme - Changelog

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
