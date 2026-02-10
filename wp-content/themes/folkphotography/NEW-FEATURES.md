# FolkPhotography Theme - New Features Guide

## 🎉 What's New

Three powerful new features have been added to your theme:

1. **Location Map Widget** 🗺️ - Interactive map showing photo locations from GPS data
2. **Camera Stats Widget** 📸 - Photography statistics dashboard 
3. **Masonry Gallery Template** 🖼️ - Pinterest-style portfolio layout

---

## 📍 Location Map Widget

### What It Does
Displays an interactive map with markers for all your photos that have GPS data. Click markers to see thumbnails and view full-size images in the lightbox.

### How to Add It

1. Go to **Appearance → Widgets**
2. Find **"Photo Location Map"** widget
3. Drag it to any widget area (Homepage - Gallery Grid, Homepage - Featured Section, or Sidebar)
4. Configure settings:
   - **Title**: e.g., "Where I've Been" or "Photo Locations"
   - **Map Height**: Default 500px, adjust as needed
   - **Filter by Category**: Optional - show only photos from a specific category

### Widget Settings

```
Title: Photo Locations
Map Height: 500px (or 400, 600, etc.)
Category: All Categories (or select one)
```

### Requirements
- Photos must have GPS data (automatically extracted by i-was-here plugin)
- Leaflet.js library (already included in theme)

### Example Usage

**Homepage Setup:**
- Widget Area: Homepage - Gallery Grid
- Title: "Around the World"
- Height: 600px
- Category: Travel

**Sidebar Setup:**
- Widget Area: Sidebar
- Title: "Recent Locations"
- Height: 400px
- Category: All Categories

---

## 📊 Camera Stats Widget

### What It Does
Automatically displays photography statistics from your EXIF data:
- Total photos uploaded
- Most used camera
- Favorite lens
- Preferred aperture
- Most common ISO
- Number of shooting locations

### How to Add It

1. Go to **Appearance → Widgets**
2. Find **"Camera & Photography Stats"** widget
3. Drag it to any widget area
4. Set the title (default: "By The Numbers")
5. Save!

No other configuration needed - it automatically pulls stats from your photo library.

### Widget Settings

```
Title: By The Numbers (or "Camera Gear", "My Setup", etc.)
```

### Requirements
- Photos must have EXIF data
- i-was-here plugin extracts and stores this data automatically

### Example Usage

**Homepage Setup:**
- Widget Area: Homepage - Featured Section
- Title: "My Photography Journey"

**Sidebar Setup:**
- Widget Area: Sidebar
- Title: "Camera Stats"

### What Stats Are Shown

1. **Total Photos** - Count of images with EXIF data
2. **Favorite Camera** - Most frequently used camera body
3. **Favorite Lens** - Most frequently used lens
4. **Favorite Aperture** - Your go-to aperture setting
5. **Most Used ISO** - Your preferred ISO setting
6. **Shooting Locations** - Number of unique GPS locations

---

## 🖼️ Masonry Gallery Template

### What It Is
A Pinterest-style page template that displays your portfolio/blog posts in a beautiful staggered grid layout. Images are different heights, creating an organic, flowing layout.

### How to Use It

1. **Create a New Page:**
   - Go to **Pages → Add New**
   - Give it a title (e.g., "Portfolio", "Gallery", "My Work")

2. **Select the Template:**
   - In the page editor, look for **"Page Attributes"** or **"Template"** box
   - Select **"Masonry Gallery"** from the dropdown
   - Add optional intro text in the editor

3. **Publish the Page**

That's it! The page will automatically display your portfolio items and blog posts in a masonry layout.

### Features

#### **Built-in Filters**
The page includes filter buttons to narrow results:
- **Type Filter**: All / Portfolio / Blog Posts
- **Category Filter**: All / (your categories)

Users can click these to filter what's shown.

#### **Lightbox Integration**
- Click any image to open in GLightbox
- Shows EXIF data in the lightbox (camera, lens, settings)
- "View Full Post" link to the actual post

#### **Responsive Design**
- Desktop: 3 columns
- Tablet: 2 columns
- Mobile: 1 column
- Automatic layout recalculation

### Customization

#### Page Content
Add intro text in the WordPress editor. It will appear centered above the gallery:

```
Title: Portfolio

Content:
A collection of my best work from the past year. 
Each image tells a story from my photographic journey.
```

#### URL Parameters
You can create direct links to filtered views:

```
yoursite.com/portfolio/                    # All items
yoursite.com/portfolio/?type=portfolio     # Portfolio only
yoursite.com/portfolio/?type=posts         # Blog posts only
yoursite.com/portfolio/?cat=5              # Specific category
yoursite.com/portfolio/?type=portfolio&cat=5  # Combo
```

---

## 🚀 Suggested Homepage Setup

Here's a recommended homepage layout using all your widgets:

### **Homepage Widget Areas**

#### 1. Homepage - After Hero
**Widget:** Recent Portfolio Items
- Title: "Latest Work"
- Number: 6 items
- Columns: 3

#### 2. Homepage - Featured Section  
**Widget:** Camera & Photography Stats
- Title: "By The Numbers"

#### 3. Homepage - Gallery Grid
**Widget:** Photo Location Map
- Title: "Around the World"
- Height: 600px
- Category: All Categories

### Visual Layout

```
┌─────────────────────────────────┐
│     Hero Image (Parallax)        │
└─────────────────────────────────┘
┌─────────────────────────────────┐
│      Latest Work (Grid)          │
│   [img] [img] [img]              │
│   [img] [img] [img]              │
└─────────────────────────────────┘
┌─────────────────────────────────┐
│    By The Numbers (Stats)        │
│  📷 Total: 1,234 photos          │
│  📸 Camera: Canon EOS R5          │
│  🔍 Lens: 24-70mm f/2.8          │
└─────────────────────────────────┘
┌─────────────────────────────────┐
│   Around the World (Map)         │
│        🗺️ [Interactive Map]     │
└─────────────────────────────────┘
```

---

## 💡 Usage Tips

### Location Map Widget

**Best Practices:**
- Use in full-width areas for best effect
- Set height to 500-600px for good visibility
- Filter by "Travel" category for travel photography showcase
- Works great on homepage or dedicated "Locations" page

**Pro Tip:** Create a dedicated page titled "Where I've Been" with just the map widget at 800px height!

### Camera Stats Widget

**Best Practices:**
- Works great in sidebars or featured sections
- Updates automatically as you upload more photos
- Great for "About" or "Gear" pages
- Shows your photographic evolution over time

**Pro Tip:** The stats update in real-time, so as you upload more photos, the widget reflects your changing preferences!

### Masonry Gallery Template

**Best Practices:**
- Use for main portfolio pages
- Great for "All Work", "Best Of", or category-specific pages
- Let users filter - they'll love the interactivity
- Images look best when they vary in aspect ratio

**Pro Tip:** Create multiple gallery pages:
- `/portfolio/` - All portfolio items
- `/blog-photos/` - Blog photography
- `/travel/` - Travel photography (use ?cat= parameter)

---

## 🛠️ Troubleshooting

### Location Map Not Showing Photos

**Problem:** Map displays but no markers appear

**Solutions:**
1. Check that photos have GPS data:
   - Go to Media Library
   - Edit an image
   - Look for "Location Data" meta box
   - Should show latitude/longitude

2. Run the i-was-here scanner:
   - Go to **Tools → i-was-here Scanner**
   - Click "Scan All Images"
   - Wait for completion

3. Check widget category filter:
   - Make sure selected category contains photos with GPS
   - Try "All Categories" to see all photos

### Camera Stats Widget Shows "No Data"

**Problem:** Widget says "No camera data available"

**Solutions:**
1. Upload photos with EXIF data
   - Photos must come from a camera (not screenshots)
   - EXIF data must be intact (not stripped)

2. Check i-was-here plugin is active:
   - Go to **Plugins**
   - Make sure "i-was-here" is activated

3. Scan existing images:
   - **Tools → i-was-here Scanner**
   - Click "Scan All Images"

### Masonry Layout Looks Weird

**Problem:** Images overlapping or gaps in layout

**Solutions:**
1. Hard refresh the page (Ctrl + F5 or Cmd + Shift + R)
2. Clear browser cache
3. Make sure JavaScript is enabled
4. Check browser console for errors (F12)

### Map Shows Wrong Location

**Problem:** Photo markers appear in incorrect locations

**Solution:**
- GPS coordinates are pulled from EXIF - check the original image
- You can manually edit coordinates:
  - Media Library → Edit image
  - Find "Location Data" meta box
  - Enter correct latitude/longitude

---

## 📋 Widget Reference

### All Available Widgets

1. **Recent Portfolio Items** (existing)
   - Shows latest portfolio collections
   - Configurable grid (2, 3, 4, or 6 columns)

2. **Category Gallery** (existing)
   - Shows images from specific category
   - Instagram-style grid

3. **Photo Location Map** (NEW!)
   - Interactive map of photo locations
   - Lightbox integration

4. **Camera & Photography Stats** (NEW!)
   - Auto-generated statistics
   - No configuration needed

### Where to Use Each Widget

**Homepage - After Hero:**
- Recent Portfolio Items ⭐⭐⭐
- Category Gallery ⭐⭐

**Homepage - Featured Section:**
- Camera Stats ⭐⭐⭐
- Recent Portfolio Items ⭐⭐

**Homepage - Gallery Grid:**
- Photo Location Map ⭐⭐⭐
- Category Gallery ⭐⭐⭐

**Sidebar:**
- Camera Stats ⭐⭐
- Photo Location Map ⭐

---

## 🎨 Customizing Styles

All new features use your theme's existing dark color scheme. To customize colors, edit `style.css`:

### Camera Stats Colors

```css
/* Location: style.css, line ~1200 */
.stat-item {
    border-left: 3px solid var(--accent-primary); /* Change border color */
}

.stat-value {
    color: var(--white-bright); /* Change stat number color */
}
```

### Map Colors

```css
/* Location: style.css, line ~1260 */
.leaflet-popup-content-wrapper {
    background: var(--grey-darkest); /* Change popup background */
}
```

### Masonry Overlay

```css
/* Location: style.css, line ~1415 */
.masonry-overlay {
    background: linear-gradient(...); /* Change overlay gradient */
}
```

---

## 🎯 Next Steps

1. **Add Widgets to Homepage:**
   - Appearance → Widgets
   - Configure all three widget areas

2. **Create Masonry Gallery Page:**
   - Pages → Add New
   - Select "Masonry Gallery" template
   - Publish

3. **Upload Photos with GPS:**
   - Use photos from your camera
   - i-was-here will extract GPS automatically
   - Run scanner for existing photos

4. **Test Everything:**
   - View homepage - all widgets should appear
   - Check masonry page - images in staggered grid
   - Click map markers - should open lightbox

5. **Customize:**
   - Adjust widget titles
   - Try different layouts
   - Create multiple gallery pages

---

## 📚 Additional Resources

- **Theme Documentation:** README.md
- **Getting Started Guide:** GETTING-STARTED.md
- **i-was-here Plugin:** Check plugin folder for documentation

---

**Built with ❤️ for FolkPhotography Theme**
*Last Updated: February 2026*
