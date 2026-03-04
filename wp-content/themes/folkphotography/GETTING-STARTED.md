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

### **Posts** (Blog/Stories)
- Photo essays with narrative
- Behind-the-scenes stories
- Travel journals
- Use for: Content with story + multiple images

### **Portfolio** (NEW! Custom Post Type)
- Curated collections of your best work
- Organized by theme/project/series
- Examples:
  - "Iceland Adventure 2025"
  - "Black & White Street Series"
  - "Dog Portraits Collection"
  - "Holga Experiments"

### **Products** (WooCommerce)
- Individual prints for sale
- Digital downloads
- Keep separate from Portfolio (can link between them)

---

## Homepage Customization Strategy

### **Your Homepage Sections** (Top to Bottom)

1. **Hero Image** (Fullscreen Parallax)
   - Configure: Appearance → Customize → Hero Image Settings
   - Select a category (e.g., "Best Work" or "Featured")
   - Displays random image from that category

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
   - Create a category called "Hero Images"
   - Upload 5–10 of your best landscape-oriented images to the Media Library
   - In the Media Library, open each image → assign it to the "Hero Images" category
     *(Note: the theme registers categories for attachments, so media files can have categories)*
   - Go to Appearance → Customize → Hero Image Settings
   - Select "Hero Images" from the dropdown
   - Set parallax speed (default 0.5 is a good starting point)
   - Save & Publish

5. **Create Menu**
   - Appearance → Menus → Create "Primary Menu"
   - Add: Home, Portfolio, Journal, Prints (Shop), About, Contact
   - Assign to "Primary Menu" location → Save

### Phase 2: Homepage Widgets (20 minutes)

The homepage has three widget areas below the hero. Go to **Appearance → Widgets** to build them out. You must have the Reading settings configured (Phase 1, Step 2) before the widgets will display correctly on the front page.

**Available custom widgets for the homepage:**
- **Recent Portfolio Items** — grid of your Portfolio post type entries
- **Category Gallery** — grid of post featured images from a specific category (opens in lightbox)
- **Photo Location Map** — interactive Leaflet map of GPS-tagged photos
- **Camera & Photography Stats** — auto-generated stats from EXIF data (total photos, favorite camera, lens, etc.)

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
- [ ] Create main categories (Astro, Wildlife, Street, etc.)
- [ ] Create "Hero Images" category
- [ ] Upload 5-10 hero images, assign them to "Hero Images" category in Media Library
- [ ] Configure hero in Customizer (Appearance → Customize → Hero Image Settings)
- [ ] Create primary menu (Home, Portfolio, Journal, Prints, About, Contact)
- [ ] Add homepage widgets (After Hero, Featured, Gallery Grid)
- [ ] Upload first 10-20 photos with categories and featured images set
- [ ] Create first Portfolio collection
- [ ] Set up at least one product (if selling)

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
A: Posts = stories/blogs with narrative (your Journal). Portfolio = curated collections of your best work, displayed in image grids.

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
