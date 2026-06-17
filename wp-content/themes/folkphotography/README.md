# FolkPhotography WordPress Theme

A dark, minimalist photo-centric WordPress theme featuring fullscreen hero images with parallax scrolling effects.

## Features

- **Fullscreen Hero Images**: Random hero rotation — mark any image in the Media Library with the "Hero Rotation" checkbox; no category required
- **Parallax Scrolling**: Smooth parallax effect as users scroll down from the hero section
- **Dark Theme**: Elegant dark color scheme optimized for photography
- **Minimalistic Navigation**: Clean, unobtrusive header that adapts to scrolling
- **Responsive Design**: Mobile-friendly layout that works on all devices
- **WooCommerce Support**: Full integration with WooCommerce for selling prints and products
- **Integration with I-Was-Here Plugin**: Works seamlessly with photo metadata and location data
- **Custom Gutenberg Block**: `folkphotography/masonry-gallery` — drop a masonry photo grid anywhere in a post or page, with category picker, column count, and GLightbox integration
- **Five Custom Widgets**: Recent Portfolio, Category Gallery, Photo Location Map, Camera & Photography Stats, Random Category Photos
- **Masonry Gallery Page Template**: Full-page masonry grid with type and category filtering
- **Customizable**: Theme customizer options for parallax speed

## Installation

1. Upload the `folkphotography` folder to `/wp-content/themes/`
2. Activate the theme through the 'Themes' menu in WordPress
3. Go to Appearance > Customize to configure theme settings

## Configuration

### Hero Image Setup

Hero images are selected per-image in the Media Library — no category required.

1. Go to **Media > Library** (list view)
2. Click any image to open its edit panel
3. Check **"Use in homepage hero rotation"** and click Update
4. Repeat for as many images as you want in the rotation (5–20 recommended)
5. Go to **Appearance > Customize > Hero Image Settings** to adjust parallax speed (0.1 = slow, 1.0 = fast)

The theme picks a random marked image on each page load. The Customizer panel shows a live count of how many images are currently in rotation.

### Menu Setup

1. Go to **Appearance > Menus**
2. Create a new menu and assign it to the "Primary Menu" location
3. Add your desired pages/links to the menu

## Theme Structure

```
folkphotography/
├── style.css                        # Theme header (version here) + all styles
├── functions.php                    # Enqueues, CPT/taxonomy registration, widget areas, includes
├── front-page.php                   # Homepage template (hero + widget areas)
├── header.php                       # Site header and navigation
├── footer.php                       # Site footer
├── index.php                        # Blog/archive fallback template
├── single.php                       # Single post template
├── page.php                         # Standard page template
├── 404.php                          # 404 error page
├── inc/
│   ├── widgets.php                  # All 5 custom widget classes
│   ├── blocks.php                   # Gutenberg block registration + render callbacks
│   └── media-admin.php              # Admin media library enhancements (hero toggle, filters)
├── js/
│   ├── main.js                      # Parallax, header scroll, mobile menu, GLightbox init
│   ├── block-masonry-gallery.js     # Editor JS for folkphotography/masonry-gallery block
│   └── media-grid.js                # Media Library grid enhancements
├── page-templates/
│   └── masonry-gallery.php          # Masonry Gallery page template (CSS columns, filtering)
├── template-parts/
│   └── content.php                  # Post excerpt partial
└── woocommerce/
    └── archive-product.php          # WooCommerce shop page override
```

## Customization

### Colors

The theme uses CSS custom properties (variables) defined in `style.css`. You can easily customize the color scheme by modifying the `:root` variables:

```css
:root {
    --black-pure: #000000;
    --grey-darkest: #121212;
    --white-bright: #e8e8e8;
    /* ... etc */
}
```

### Parallax Speed

The parallax effect speed can be adjusted in the theme customizer or by modifying the `data-parallax-speed` attribute in `front-page.php`.

## Integration with I-Was-Here Plugin

This theme is designed to work alongside the I-Was-Here plugin. The hero image fallback will automatically select images that have EXIF data if no category is specified.

The plugin now extracts and displays:
- Camera make and model
- Lens information
- ISO, Aperture, Shutter Speed
- Focal Length
- Date taken
- GPS coordinates

## WooCommerce Integration

The theme includes complete WooCommerce support for selling photography prints and products:

### Features
- **Product Grid**: Responsive 3-column grid on desktop, adapts to 2 columns on tablet, single column on mobile
- **Dark-themed Shop**: All WooCommerce elements styled to match the dark photography aesthetic
- **Product Gallery**: Full support for product image zoom, lightbox, and slider
- **Shopping Cart & Checkout**: Custom-styled cart and checkout pages
- **Product Reviews**: Styled review system integrated with the dark theme
- **Responsive Design**: All WooCommerce pages are fully responsive

### Default Settings
- Products per page: 12
- Products per row: 3 (desktop)
- Product gallery features: zoom, lightbox, slider all enabled

You can customize these settings in `functions.php` under the WooCommerce Support section.

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Credits

- Developed by Zachary Folk
- Fonts: Google Fonts (Lato, Poppins, Rajdhani)

## License

GNU General Public License v3.0

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for full version history.
