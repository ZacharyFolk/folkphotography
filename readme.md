```
============================= [o] FOLK PHOTOGRAPHY [o] =============================

                                        ___
                               .==.____/[=]\ _______
                               |_---___________[o]_|
                              /|    _ /  --  \     |\
                              ||   (_// /''\ \\    ||
                              ||     \\ \../ //    ||
                              \|      \\ __ //     |/
                               ---------------------

====================================================================================
```

# FolkPhotography

A custom WordPress theme built for photography portfolios — dark, fast, and image-forward.

Built and maintained by [Zachary Folk](https://folkphotography.com).

---

## What This Is

FolkPhotography is the WordPress theme for the website at the same domain. This domain has been around for about 30 years now and there have been dozens of themes and platforms that have run this. From all of those lessons and different things I have wanted from a theme I have built this one.

It combines a curated **Portfolio** section (custom post type, masonry archive, full-bleed single view) with a narrative **Journal** (standard blog with category filtering), a **print shop** via WooCommerce, and deep integration with the [i-was-here](https://github.com/ZacharyFolk/i-was-here) plugin for automatic EXIF and GPS extraction.

The design goal is to keep attention on the photography with minimal UI and a dark palette.

This is a labor of love and I share in case it inspires or helps anyone else. I welcome all feedback and you can contribute if you are so inclined. It is forever a WIP and I don't imagine I will ever stop tinkering and updating it.

---

## Features

- **Fullscreen hero with parallax** — random rotation from images you flag in the Media Library; no category setup required
- **Portfolio CPT** — `portfolio` post type with `portfolio_category` and `portfolio_tag` taxonomies; masonry archive + full-bleed single template with EXIF panel and same-category navigation
- **Journal with category filtering** — blog archive filterable by category; category and tag archives use the same journal layout
- **Masonry Gallery page template** — CSS `column-count` masonry (no JS layout), dual filtering by type and category, GLightbox with EXIF in lightbox descriptions
- **Masonry Gallery Gutenberg block** — drop a masonry grid anywhere in the editor; category picker, columns (2–4), photo count (4–30), server-side rendered
- **Five custom widgets** — Recent Portfolio Items, Category Gallery, Photo Location Map (Leaflet), Camera & Photography Stats, Random Category Photos (masonry / grid / strip layouts)
- **WooCommerce integration** — theme support for a print shop: dark-styled shop, cart, and checkout pages are ready; adding products and configuring the store is a separate setup step (see GETTING-STARTED.md)
- **EXIF display** — camera, lens, focal length, aperture, shutter, ISO, and location shown on portfolio singles; pulled from `_iwh_*` meta set by the i-was-here plugin
- **Mobile-responsive** — responsive menu, CSS-based masonry that works at any viewport
- **Admin enhancements** — hero toggle in Media Library grid view, filter options, thumbnail column in post and portfolio admin lists

---

## Theme Structure

```
wp-content/themes/folkphotography/
├── style.css                        # Theme header + all styles
├── functions.php                    # Enqueues, CPT/taxonomy, widget areas, image sizes
├── front-page.php                   # Homepage (hero + widget areas)
├── header.php                       # Site header and navigation
├── footer.php                       # Site footer
├── index.php                        # Journal/blog archive + category/tag fallback
├── single.php                       # Single blog post
├── single-portfolio.php             # Single portfolio item (full-bleed hero, EXIF panel)
├── archive-portfolio.php            # Portfolio archive (masonry grid, category filter)
├── page.php                         # Standard page template
├── 404.php                          # 404 error page
├── inc/
│   ├── widgets.php                  # All 5 custom widget classes
│   ├── blocks.php                   # Gutenberg block registration + render callbacks
│   └── media-admin.php              # Admin media library enhancements
├── js/
│   ├── main.js                      # Parallax, header scroll, mobile menu, GLightbox init
│   ├── block-masonry-gallery.js     # Editor JS for folkphotography/masonry-gallery block
│   └── media-grid.js                # Media Library grid enhancements
├── page-templates/
│   └── masonry-gallery.php          # Masonry Gallery page template
├── template-parts/
│   └── content.php                  # Post excerpt partial (journal/archive loop)
└── woocommerce/
    └── archive-product.php          # WooCommerce shop page override
```

---

## Documentation

Full documentation lives inside the theme directory:

| File                                                                                 | Purpose                                    |
| ------------------------------------------------------------------------------------ | ------------------------------------------ |
| [GETTING-STARTED.md](wp-content/themes/folkphotography/GETTING-STARTED.md)           | First-time setup guide                     |
| [ROADMAP.md](wp-content/themes/folkphotography/ROADMAP.md)                           | Active task tracker and feature roadmap    |
| [CHANGELOG.md](wp-content/themes/folkphotography/CHANGELOG.md)                       | Version history                            |
| [WORKFLOW-GUIDE.md](wp-content/themes/folkphotography/WORKFLOW-GUIDE.md)             | Daily upload and curation workflow         |
| [IMAGE-METADATA-GUIDE.md](wp-content/themes/folkphotography/IMAGE-METADATA-GUIDE.md) | Preserving EXIF through editing and export |

---

## Dependencies

| Dependency                                                     | How it's used                              |
| -------------------------------------------------------------- | ------------------------------------------ |
| [i-was-here plugin](https://github.com/ZacharyFolk/i-was-here) | EXIF/GPS extraction from uploaded images   |
| [GLightbox](https://github.com/biasedbit/glightbox)            | Lightbox for galleries and portfolio grids |
| [Leaflet](https://leafletjs.com)                               | Location map widget                        |
| [WooCommerce](https://woocommerce.com)                         | Print shop (optional)                      |
| Google Fonts (Lato, Poppins, Rajdhani)                         | Typography                                 |

> **Note:** GLightbox, Leaflet, and Google Fonts are currently loaded from CDN. Self-hosting all three is tracked in the [ROADMAP](wp-content/themes/folkphotography/ROADMAP.md) as a performance optimization.

---

## Performance Target

Shooting for **100 on Lighthouse** — a score previously achieved with the simplefolk theme this project evolved from. Active optimization work is tracked in the [Performance & SEO Sprint](wp-content/themes/folkphotography/ROADMAP.md) section of the roadmap.

---

## License

GNU General Public License v3.0 — see [LICENSE.md](LICENSE.md) for details.
