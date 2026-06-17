# Quick Start Checklist

Use this checklist to get set up and start publishing. Work through the phases in order — each one builds on the last.

---

## Phase 1: Theme & WordPress Setup

### Activate & Verify
- [ ] Appearance → Themes → Activate "FolkPhotography"
- [ ] Visit the frontend — no errors, theme is loading

### Create Required Pages
- [ ] Pages → Add New → Title: `Home` → leave body blank → Publish
- [ ] Pages → Add New → Title: `Journal` → leave body blank → Publish
- [ ] Settings → Reading → "A static page" → Homepage: `Home`, Posts page: `Journal` → Save

### Create Categories
Go to **Posts → Categories** and create your main subjects:
- [ ] Astro Photography
- [ ] Wildlife & Birds
- [ ] Street Photography
- [ ] Macro/Micro
- [ ] Portraits
- [ ] Travel
- [ ] Landscape
- [ ] Film/Holga *(if applicable)*

### Create Navigation Menu
- [ ] Appearance → Menus → Create "Primary Menu"
- [ ] Add pages: Home, Portfolio, Journal, Prints, About, Contact
- [ ] Assign to "Primary Menu" location → Save

---

## Phase 2: Fix Metadata Before Uploading

**Do this before uploading any images.** EXIF data (camera, GPS, settings) must be preserved on export or it's gone.

### DxO PhotoLab
- [ ] File → Export → File Options
- [ ] Check "Keep EXIF data" and "Preserve GPS coordinates"
- [ ] Save as a preset: "Web Upload - Keep Metadata"
- [ ] Test export one image → verify metadata with right-click → Properties

### Photoshop
- [ ] Use File → Save As (not Save for Web)
- [ ] Check "Embed Color Profile"
- [ ] Verify EXIF survives: File → File Info before and after

See IMAGE-METADATA-GUIDE.md for full detail on any export tool.

---

## Phase 3: First Upload & Hero Setup

### Upload Images
- [ ] Media → Add New → drag in your first batch of images
- [ ] For each image: add a title, select one category, add 2–3 tags → Update

### Mark Hero Images
- [ ] Switch Media Library to **List view**
- [ ] Open each of your best landscape-oriented shots → check **"Use in homepage hero rotation"** → Update
- [ ] Aim for 5–10 images in rotation

### Configure Hero Speed
- [ ] Appearance → Customize → Hero Image Settings → set parallax speed (0.5 is a good default) → Save & Publish

### Verify
- [ ] Visit the homepage — hero should be rotating
- [ ] Open one image in Media Library — camera info and GPS should be populated by the i-was-here plugin

---

## Phase 4: Build the Homepage

Go to **Appearance → Widgets** and add to the three homepage widget areas:

- [ ] **Homepage - After Hero:** Add **Recent Portfolio Items** — Title: "Latest Work", 6 items, 3 columns
- [ ] **Homepage - Featured Section:** Add **Camera & Photography Stats** — Title: "By The Numbers"
- [ ] **Homepage - Gallery Grid:** Add **Category Gallery** or **Random Category Photos** — pick a category, 9 items

You can also add the **Photo Location Map** widget once you have GPS-tagged photos uploaded.

---

## Phase 5: First Content

### Upload More Photos
- [ ] Upload photos across several categories
- [ ] Set a featured image on each post or portfolio item — this is what shows in grids and lightboxes

### Write a Journal Post
- [ ] Posts → Add New
- [ ] Write about a shoot — inline images, narrative, categories, tags
- [ ] Visit `/journal/` and verify it appears with the category filter bar

### Create a Portfolio Item
- [ ] Portfolio → Add New
- [ ] Title: something specific — e.g. "Macro: Forest Floor", "Black & White Street Series"
- [ ] Write a short excerpt (1–2 sentences — shows in the lightbox preview on the archive)
- [ ] Body: insert a **Gallery block** → add your curated selection of images
- [ ] Set a **featured image** (appears in the `/portfolio/` masonry grid)
- [ ] Assign a **Portfolio Category** (controls filter buttons on the archive)
- [ ] Publish → visit `/portfolio/` to confirm it appears in the grid

### Verify the Portfolio Archive
- [ ] Click a category filter button — filtered view works
- [ ] Click an item → lightbox opens with excerpt and "View Full Portfolio Item" link
- [ ] Follow the link → full single-portfolio page with hero, gallery, EXIF panel

---

## Phase 6: Optional Setup

- [ ] Create an "About" page — write it like a human, include a photo
- [ ] Create a "Contact" page — email link or contact form
- [ ] Create a "Gallery" page → assign **Masonry Gallery** template (shows a mixed browse grid of all posts + portfolio items)
- [ ] WooCommerce: add your first print product if selling

---

## Ongoing Rhythm

Once you're set up, the workflow is simple:

- **Uploading:** Export with metadata preserved → upload → title, category, tags → done
- **Journal:** Write posts when you have a story to tell — a shoot, a trip, a technique
- **Portfolio:** When a body of work feels strong enough to stand alone, make a portfolio item — curate the best shots, write an excerpt, publish
- **Widgets:** Update widget categories as your library grows

See WORKFLOW-GUIDE.md for a more detailed system if you want one.

---

## Troubleshooting

**Metadata stripped from uploads**
→ Fix export settings first — see IMAGE-METADATA-GUIDE.md

**Location map empty**
→ Check photos have GPS data → Tools → i-was-here Scanner to reprocess

**Hero not showing**
→ Confirm at least one image is checked "Use in homepage hero rotation" in the Media Library (list view)

**Portfolio archive empty**
→ Check that portfolio items are Published and have a featured image set

---

## Documentation

| File | What's in it |
|---|---|
| GETTING-STARTED.md | Full setup guide with explanations |
| WORKFLOW-GUIDE.md | Ongoing upload and curation system |
| IMAGE-METADATA-GUIDE.md | Preserving EXIF through any export tool |
| ROADMAP.md | What's built, what's next |

---

**Last Updated:** May 2026
**Quick Start Version:** 1.2
