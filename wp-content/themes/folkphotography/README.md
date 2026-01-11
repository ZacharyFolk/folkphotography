# FolkPhotography WordPress Theme

A dark, minimalist photo-centric WordPress theme featuring fullscreen hero images with parallax scrolling effects.

## Features

- **Fullscreen Hero Images**: Display random hero images from a selected category on the homepage
- **Parallax Scrolling**: Smooth parallax effect as users scroll down from the hero section
- **Dark Theme**: Elegant dark color scheme optimized for photography
- **Minimalistic Navigation**: Clean, unobtrusive header that adapts to scrolling
- **Responsive Design**: Mobile-friendly layout that works on all devices
- **WooCommerce Support**: Full integration with WooCommerce for selling prints and products
- **Integration with I-Was-Here Plugin**: Works seamlessly with photo metadata and location data
- **WordPress Block Editor Support**: Full compatibility with Gutenberg
- **Customizable**: Theme customizer options for hero category and parallax speed

## Installation

1. Upload the `folkphotography` folder to `/wp-content/themes/`
2. Activate the theme through the 'Themes' menu in WordPress
3. Go to Appearance > Customize to configure theme settings

## Configuration

### Hero Image Setup

1. Navigate to **Appearance > Customize > Hero Image Settings**
2. Select a category that contains images you want to use as hero backgrounds
3. Adjust the parallax speed (0.1 = slow, 1 = fast)
4. The theme will randomly select an image from that category on each page load

### Menu Setup

1. Go to **Appearance > Menus**
2. Create a new menu and assign it to the "Primary Menu" location
3. Add your desired pages/links to the menu

## Theme Structure

```
folkphotography/
├── js/
│   └── main.js              # Parallax and navigation JavaScript
├── template-parts/
│   └── content.php          # Post excerpt template
├── woocommerce/
│   └── archive-product.php  # WooCommerce shop page template
├── 404.php                  # 404 error page
├── footer.php               # Site footer
├── front-page.php           # Homepage with hero section
├── functions.php            # Theme functions and setup
├── header.php               # Site header and navigation
├── index.php                # Main template file
├── page.php                 # Page template
├── single.php               # Single post template
├── style.css                # Main stylesheet
└── README.md                # This file
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

### Version 1.0.0
- Initial release
- Fullscreen hero images with parallax
- Dark minimalist design
- Responsive navigation
- WordPress block editor support
- Full WooCommerce integration
- Complete EXIF data support via I-Was-Here plugin integration
