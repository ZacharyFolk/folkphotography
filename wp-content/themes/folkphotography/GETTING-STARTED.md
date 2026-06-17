# Getting Started with FolkPhotography Theme

---

## Critical First Step: Homepage Setup

> **Do this before anything else.** Without this, your homepage will show full blog posts instead of the designed layout.

### How the Homepage Works

This theme uses a WordPress template called `front-page.php`, which **always** handles the homepage — it takes priority over any other template. You do not build the homepage with the Block Editor; the layout is hardcoded in layers:

```
1. Hero Section          ← fullscreen parallax image (configured in Customizer)
2. Widget Area: After Hero  ← you build this in Appearance → Widgets
3. Page Content          ← optional: the body of a WordPress Page you assign as Front Page
4. Widget Area: Featured
5. Widget Area: Gallery Grid
```

The "Page Content" slot in the middle is driven by WordPress's main query. If WordPress thinks it should be showing blog posts (the default), it dumps **full post content** in that slot using `the_content()` — no excerpts, no layout, just raw posts between your widget sections. That is wrong behavior.

The fix is to tell WordPress to use a specific Page for the homepage so the content slot renders cleanly.

### Step 1: Create Required Pages

Go to **Pages → Add New** and create these two pages:

| Page Title | Content | Purpose |
|---|---|---|
| `Home` | Leave blank (or add a short bio/welcome paragraph) | Assigned as Front Page — prevents posts from appearing |
| `Journal` | Leave blank | Assigned as Posts Page — this becomes your blog archive |

> The `Home` page content (if any) appears between the "After Hero" and "Featured" widget areas on your homepage. Keep it minimal — a short paragraph at most, or leave it blank entirely.

### Step 2: Configure Reading Settings

Go to **Settings → Reading** and set:

- **Your homepage displays:** A static page
- **Homepage:** Home *(the page you just created)*
- **Posts page:** Journal *(the page you just created)*

Click **Save Changes**.

**Why this matters:**
- With "A static page" selected, the homepage content slot shows only the `Home` page content (clean)
- The `Journal` page automatically becomes your blog archive, powered by `index.php`
- The hero and all three widget areas work exactly the same either way — only the middle content slot is affected

### Step 3: Verify It Works

Visit your homepage. You should see:
- Hero section (or blank if hero not yet configured)
- No blog posts dumped in the middle of the page
- Footer

The page will look minimal until you add widgets (next section) and configure the hero image.

---

## Your Photography Organization Strategy

### Quick Start - Path of Least Resistance

The theme is designed to make uploading and organizing photos as frictionless as possible. Here's your workflow:

#### **Upload Workflow (3 Steps)**
1. **Upload Photo** → WordPress Media Library
2. **Add to Post/Portfolio** → Assign 1 Category + Tags
3. **Done!** → Plugin auto-extracts EXIF data & GPS

---

## Recommended Organization System

### **1. Categories** (Primary Organization - Main Subjects)

Create these categories for your main photography types:

- **Astro Photography** - Night sky, stars, milky way
- **Wildlife & Birds** - Nature photography, birds, animals
- **Street Photography** - Urban scenes, candid moments
- **Macro/Micro** - Close-up photography
- **Portraits** - Dogs, commissioned work, street portraits
- **Travel** - Location-based photo journeys
- **Holga/Film** - Specific camera/technique work
- **Landscape** - Scenery, nature scenes

*One category per post - keeps it simple!*

### **2. Tags** (Specific Details - Multiple Allowed)

Use tags for specifics that can overlap:

**Technique Tags:**
- holga, film, digital, long-exposure, black-and-white, composite

**Subject Tags:**
- dogs, commissioned, urban, nature, architecture, people

**Location Tags:**
- The plugin handles GPS! But you can add: paris, iceland, local, etc.

**Style Tags:**
- editorial, documentary, fine-art, experimental

*Multiple tags = more discoverable*

### **3. Custom Taxonomies** (Auto-Created by Theme)

**Photo Subject** - Automatically available for fine-tuning
**Photo Style** - For technique/aesthetic classifications

---

## Content Types Explained

### **Posts** (Journal / Blog)
- Photo essays with narrative text
- Behind-the-scenes stories and shoot diaries
- Travel journals, technique notes
- Browsable at `/journal/` — filterable by category and tag
- Clicking a category (e.g. "Macro") shows all posts in that category using the same journal layout
- Use for: anything where the writing is as important as the images

### **Portfolio** (Curated Collections)
- Your highlight reel — the work you're proud enough to frame and describe
- Each portfolio item has: a full-bleed hero image, an excerpt, a gallery block body, an EXIF panel, and category/tag links
- Lives at `/portfolio/` — masonry grid filterable by Portfolio Category
- Clicking an item opens a lightbox preview; clicking through goes to the full single-portfolio page
- **How to build one:**
  1. Portfolio → Add New
  2. Write a title (specific beats generic: "Macro: Forest Floor" not "Macro Photos")
  3. Write a short excerpt — this shows in the lightbox preview on the archive
  4. In the body, insert a **Gallery block** with your curated selection of images
  5. Set a **featured image** — this is what appears in the `/portfolio/` masonry grid
  6. Assign a **Portfolio Category** — controls the category filter buttons on the archive page
  7. Publish
- **Portfolio vs Journal:** A journal post is a shoot diary — everything from the day, in order, with the story. A portfolio item is the edit — only the strongest shots, presented intentionally. The same images can appear in both; they serve different audiences.

### **Products** (WooCommerce — requires setup)
- Individual prints for sale
- Digital downloads
- WooCommerce theme support is built in (dark-styled shop, cart, checkout) but you need to add products manually
- Keep separate from Portfolio — you can link between them once both exist

---

## Homepage Customization Strategy

### **Your Homepage Sections** (Top to Bottom)

1. **Hero Image** (Fullscreen Parallax)
   - Configure speed: Appearance → Customize → Hero Image Settings
   - Mark images: Media Library (list view) → open any image → check "Use in homepage hero rotation" → Update
   - Displays a random marked image on each page load — no category needed

2. **Widget Area: After Hero**
   - **Use for:** Recent Portfolio widget
   - Shows your latest curated collections
   - **Widgets:** Recent Portfolio Items (6-9 images, 3 columns)

3. **Page Content**
   - Your homepage content from WordPress Pages
   - **Use for:** Welcome message, bio, featured text

4. **Widget Area: Featured Section**
   - **Use for:** Featured WooCommerce Product
   - Highlight a print for sale
   - **Widgets:** WooCommerce Products, Custom HTML

5. **Widget Area: Gallery Grid**
   - **Use for:** Category Gallery widget
   - Instagram-style grid from one category
   - **Widgets:** Category Gallery (e.g., 9 recent from "Travel")

---

## Recommended Menu Structure

### **Primary Menu** (Header Navigation)

```
Home
Portfolio (dropdown)
  ├─ Astro Photography
  ├─ Wildlife & Birds
  ├─ Street Photography
  ├─ Macro/Micro
  ├─ Portraits
  └─ Travel
Journal (Blog)
Prints (Shop)
About
Contact
```

### **Footer Menu** (Optional Secondary)

```
Privacy Policy
Terms
Instagram
```

---

## Step-by-Step Setup Guide

### Phase 1: Basic Setup (30–45 minutes)

1. **Activate Theme**
   - Appearance → Themes → Activate "FolkPhotography"

2. **Set Up Homepage (Critical — do this first)**
   - Pages → Add New → Title: `Home` → Leave body blank → Publish
   - Pages → Add New → Title: `Journal` → Leave body blank → Publish
   - Settings → Reading → Homepage displays: **A static page**
   - Homepage: `Home` | Posts page: `Journal` → Save Changes
   - *(See "Critical First Step: Homepage Setup" section above for full explanation)*

3. **Create Categories**
   - Posts → Categories → Add: Astro, Wildlife, Street, Macro, Portraits, Travel, Film/Holga, Landscape

4. **Configure Hero Image**
   - Upload 5–10 of your best landscape-oriented images to the Media Library
   - Switch to **List view** in the Media Library
   - Click each image → check **"Use in homepage hero rotation"** → click Update
   - Repeat for each hero candidate — the theme picks one at random on each page load
   - Go to Appearance → Customize → Hero Image Settings → set parallax speed (0.5 is a good default) → Save & Publish
   - The Customizer panel shows a live count of how many images are currently in rotation

5. **Create Menu**
   - Appearance → Menus → Create "Primary Menu"
   - Add: Home, Portfolio, Journal, Prints (Shop), About, Contact
   - Assign to "Primary Menu" location → Save

### Phase 2: Homepage Widgets (20 minutes)

The homepage has three widget areas below the hero. Go to **Appearance → Widgets** to build them out. You must have the Reading settings configured (Phase 1, Step 2) before the widgets will display correctly on the front page.

**Available custom widgets (five total):**
- **Recent Portfolio Items** — grid of your latest Portfolio items
- **Category Gallery** — grid of post featured images from a specific category, opens in lightbox
- **Photo Location Map** — interactive Leaflet map of all GPS-tagged photos
- **Camera & Photography Stats** — auto-generated stats from EXIF data (total photos, favorite camera, lens, ISO, location)
- **Random Category Photos** — randomized photos from a chosen category in masonry, grid, or horizontal strip layout; refreshes on each page load

**Recommended starting configuration:**

1. **Homepage - After Hero** widget area:
   - Add: **Recent Portfolio Items**
   - Title: `Latest Work`
   - Number of items: 6
   - Columns: 3

2. **Homepage - Featured Section** widget area:
   - Add: **Camera & Photography Stats**
   - Title: `By The Numbers`
   - *(No configuration needed — auto-reads from EXIF data)*

3. **Homepage - Gallery Grid** widget area:
   - Add: **Category Gallery**
   - Title: `Recent Adventures` (or name a specific category)
   - Category: choose one of your main categories (e.g., Travel, Landscape)
   - Number: 9
   - Columns: 3

You can also add a **Photo Location Map** to any widget area — it works best once you have photos with GPS data uploaded.

### Phase 3: Start Uploading! (Ongoing)

**Simple Upload Process:**

1. Upload image to Media Library
2. Create Post or Portfolio item
3. Add image, write description
4. Assign 1 category + relevant tags
5. Publish!

*The plugin automatically extracts all EXIF data including GPS!*

---

## Organization Tips

### Path of Least Resistance

**Don't overthink it!** Here's the minimum:

1. Upload photo
2. Pick ONE category
3. Add 2-3 tags
4. Publish

You can always:
- Add more tags later
- Curate into Portfolios later
- Create products later

### Batch Processing

When uploading multiple photos from one shoot:

1. Upload all images at once
2. Bulk Edit → Assign category to all
3. Create one Portfolio item
4. Add images as gallery
5. Done!

### Future-Proofing

As you upload, the theme captures:
- All EXIF data (ISO, aperture, camera, lens)
- GPS coordinates (auto-mapped!)
- Categories and tags
- Dates and metadata

This builds a searchable, filterable database of your work.

---

## Advanced Customization Ideas

### Portfolio Collections to Create

- **"Best of [Year]"** - Annual highlights
- **"Black & White Series"** - Monochrome work
- **"Urban Explorer"** - City photography
- **"Commissioned Work"** - Client projects
- **"Personal Projects"** - Experimental/passion projects

### Widget Combinations

**Homepage Setup 1: Portfolio Focus**
- After Hero: Recent Portfolio (6 items, 3 cols)
- Gallery: None (keep focus on curated work)

**Homepage Setup 2: Active Blog**
- After Hero: Recent Posts (blog entries)
- Gallery: Category Gallery (9 recent from Travel)

**Homepage Setup 3: Shop Focus**
- After Hero: Featured Products (WooCommerce)
- Gallery: Recent Portfolio (show what you sell)

---

## Getting Started Checklist

- [ ] Activate FolkPhotography theme
- [ ] Create `Home` page (blank body) and `Journal` page (blank body)
- [ ] Settings → Reading → set Homepage to `Home`, Posts page to `Journal`
- [ ] Create main categories (Astro, Wildlife, Street, Macro, Portraits, Travel, Film/Holga, Landscape)
- [ ] Upload 5–10 hero images → in Media Library (list view) check "Use in homepage hero rotation" on each
- [ ] Appearance → Customize → Hero Image Settings → set parallax speed → Save
- [ ] Create primary menu (Home, Portfolio, Journal, Prints, About, Contact)
- [ ] Add homepage widgets (After Hero: Recent Portfolio; Featured: Camera Stats; Gallery Grid: Category Gallery or Random Photos)
- [ ] Upload first 10–20 photos — assign categories, set featured images
- [ ] Create first Portfolio item (title, excerpt, Gallery block body, featured image, Portfolio Category)
- [ ] Visit `/portfolio/` to verify the archive grid and single-item page look correct
- [ ] Set up at least one product in WooCommerce (if selling prints)

---

## Need Help?

**Common Questions:**

**Q: Should I set the homepage to "Latest posts" or "A static page"?**
A: Always use **A static page**. See the "Critical First Step: Homepage Setup" section at the top of this document. If you leave it on "Latest posts", full blog post content gets injected between your widget areas because `front-page.php` calls `the_content()` (not an excerpt) in its content loop.

**Q: I set a static page but the Home page shows a title or blank space — how do I remove it?**
A: In the `front-page.php` template, the page title only shows if `get_the_title()` is non-empty, and content only shows if the Home page has body content. Just leave the Home page body blank and the title/header section won't appear. If a title still shows, you can also leave the page title blank (WordPress will use a URL slug instead, which isn't displayed).

**Q: How do I control what shows on homepage?**
A: Three ways, in order of visual priority: (1) Hero image — configured in Appearance → Customize → Hero Image Settings. (2) Widget areas — Appearance → Widgets, add to the three Homepage areas. (3) Page content — edit the "Home" page to add a welcome paragraph (shows between After Hero and Featured widget areas).

**Q: How does the blog / Journal page work?**
A: Once you assign a "Posts page" in Settings → Reading, WordPress automatically uses `index.php` to render that page as a blog archive. You never need to edit the Journal page content — WordPress takes over.

**Q: Should I use Posts or Portfolio?**
A: Posts = your Journal — narrative writing with inline photos, shoot diaries, anything where the story matters. Portfolio = curated collections of your strongest images, built with a Gallery block and presented image-first. The same photos can appear in both. The journal is for volume and chronology; the portfolio is for curation and impression.

**Q: How does category filtering on the Journal work?**
A: The Journal page (`/journal/`) shows a category filter bar at the top. Clicking a category navigates to that category's archive (e.g. `/category/macro/`) which uses the same journal layout with only those posts showing. The active category is highlighted in the filter bar. Tag archives work the same way but don't show filter buttons — just the filtered post list with the tag name in the header.

**Q: How many categories should I use?**
A: Start with 5-8 main subjects. You can always add more.

**Q: Do I need to add GPS manually?**
A: No! The I-Was-Here plugin auto-extracts GPS from your images.

**Q: Can I sell prints?**
A: Yes! WooCommerce is fully integrated. Add products normally.

**Q: How do I create a photo grid?**
A: Use the Category Gallery widget in any widget area. It pulls from Post categories — make sure your posts have a featured image set.

---

## Pro Tips

1. **Use the "Featured Image"** - Always set a featured image for posts and portfolios
2. **Write Good Titles** - Helps with SEO and organization
3. **Use Excerpts** - Shows in grids and archives, keep it short
4. **Tag Consistently** - Decide on tag naming convention early (lowercase, hyphens)
5. **Curate Slowly** - Upload regularly, curate into Portfolios monthly
6. **GPS is Gold** - Images with GPS show on maps automatically!
7. **Start Simple** - Add one section at a time, don't overwhelm yourself

---

## Your First Week Plan

**Day 1:** Setup theme, create categories, configure hero
**Day 2:** Upload 20 of your best images, assign categories
**Day 3:** Create first Portfolio collection
**Day 4:** Set up homepage widgets
**Day 5:** Create first blog post about your work
**Day 6:** If selling: Add first product
**Day 7:** Share your new site!

Remember: The goal is to make uploading and sharing your work **easy and enjoyable**. Start simple, build gradually!

---

## Lightbox Features

All images automatically open in beautiful GLightbox with:
- Zoom capability
- Touch/swipe navigation
- Keyboard controls (arrows, ESC)
- Captions from image title
- "View Full Post" links in gallery widgets

Just add images - lightbox works automatically!
