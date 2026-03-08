<?php

define( 'FOLKPHOTO_VERSION', '1.1.5' );

// =============================================================================
// THEME SETUP
// =============================================================================

/**
 * Core theme setup: support flags, menus, image sizes, taxonomy extensions.
 */
function folkphotography_setup() {
    load_theme_textdomain( 'folkphotography', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );

    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'folkphotography' ),
        'footer'  => __( 'Footer Menu', 'folkphotography' ),
    ) );

    // Named sizes used throughout templates and widgets
    add_image_size( 'hero-fullscreen', 2560, 1440, true );
    add_image_size( 'hero-desktop',    1920, 1080, true );
    add_image_size( 'portfolio-large', 1200, 1200, false );
    add_image_size( 'portfolio-medium', 800,  800, false );
    add_image_size( 'gallery-thumb',    400,  400, true );

    // Allow standard categories and tags to be assigned to media attachments.
    // Required for hero image filtering and the Category Gallery widget.
    register_taxonomy_for_object_type( 'category', 'attachment' );
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
add_action( 'after_setup_theme', 'folkphotography_setup' );

// =============================================================================
// WIDGET AREAS
// =============================================================================

/**
 * Register all sidebar / widget areas.
 *
 * Homepage areas are rendered in front-page.php in this order:
 *   1. homepage-after-hero  — below the hero image
 *   2. homepage-featured    — mid-page featured section
 *   3. homepage-gallery     — bottom gallery grid
 *
 * sidebar-1 is registered for future use (e.g. a blog sidebar template).
 */
function folkphotography_widgets_init() {
    $shared = array(
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    );

    register_sidebar( array_merge( $shared, array(
        'name'          => __( 'Homepage - After Hero', 'folkphotography' ),
        'id'            => 'homepage-after-hero',
        'description'   => __( 'Appears on the homepage immediately after the hero section.', 'folkphotography' ),
        'before_widget' => '<section id="%1$s" class="widget homepage-widget %2$s">',
    ) ) );

    register_sidebar( array_merge( $shared, array(
        'name'          => __( 'Homepage - Featured Section', 'folkphotography' ),
        'id'            => 'homepage-featured',
        'description'   => __( 'Featured content area on the homepage, below page content.', 'folkphotography' ),
        'before_widget' => '<section id="%1$s" class="widget featured-widget %2$s">',
    ) ) );

    register_sidebar( array_merge( $shared, array(
        'name'          => __( 'Homepage - Gallery Grid', 'folkphotography' ),
        'id'            => 'homepage-gallery',
        'description'   => __( 'Image gallery section at the bottom of the homepage.', 'folkphotography' ),
        'before_widget' => '<section id="%1$s" class="widget gallery-widget %2$s">',
    ) ) );

    register_sidebar( array_merge( $shared, array(
        'name'          => __( 'Sidebar', 'folkphotography' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'General-purpose sidebar, available for future sidebar templates.', 'folkphotography' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
    ) ) );
}
add_action( 'widgets_init', 'folkphotography_widgets_init' );

// =============================================================================
// SCRIPTS & STYLES
// =============================================================================

/**
 * Enqueue all front-end assets.
 */
function folkphotography_scripts() {
    // Google Fonts
    wp_enqueue_style(
        'folkphotography-fonts',
        'https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Poppins:wght@200;300;400;600&family=Rajdhani:wght@300;400;500&display=swap',
        array(),
        null
    );

    // Leaflet (maps)
    wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );

    // GLightbox
    wp_enqueue_style( 'glightbox', 'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css', array(), '3.2.0' );

    // Theme stylesheet
    wp_enqueue_style( 'folkphotography-style', get_stylesheet_uri(), array(), FOLKPHOTO_VERSION );

    // Leaflet JS
    wp_enqueue_script( 'leaflet',   'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',                              array(),          '1.9.4',  true );

    // GLightbox JS
    wp_enqueue_script( 'glightbox', 'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js',        array(),          '3.2.0',  true );

    // Main theme JS — depends on GLightbox being present
    wp_enqueue_script( 'folkphotography-main', get_template_directory_uri() . '/js/main.js', array( 'glightbox' ), FOLKPHOTO_VERSION, true );

    wp_localize_script( 'folkphotography-main', 'folkphotoData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'folkphoto_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'folkphotography_scripts' );

// =============================================================================
// THEME CUSTOMIZER
// =============================================================================

/**
 * Register Customizer settings and controls.
 *
 * Adds a "Hero Image Settings" section under Appearance → Customize with:
 *   - parallax_speed : parallax scroll multiplier (0.1 = slow, 1.0 = fast)
 *
 * Hero images are selected per-image in the Media Library using the
 * "Use in Hero Rotation" checkbox — no category required.
 */
function folkphotography_customizer( $wp_customize ) {
    $wp_customize->add_section( 'folkphotography_hero', array(
        'title'       => __( 'Hero Image Settings', 'folkphotography' ),
        'description' => __( 'To add images to the hero rotation: open any image in Media Library and check "Use in Hero Rotation".', 'folkphotography' ),
        'priority'    => 30,
    ) );

    $wp_customize->add_setting( 'parallax_speed', array(
        'default'           => 0.5,
        'sanitize_callback' => 'folkphotography_sanitize_decimal',
    ) );

    $wp_customize->add_control( 'parallax_speed', array(
        'label'       => __( 'Parallax Speed', 'folkphotography' ),
        'description' => __( '0.1 = very slow, 0.5 = default, 1.0 = fast', 'folkphotography' ),
        'section'     => 'folkphotography_hero',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0.1,
            'max'  => 1.0,
            'step' => 0.1,
        ),
    ) );
}
add_action( 'customize_register', 'folkphotography_customizer' );

/**
 * Sanitize a decimal/float value for Customizer settings.
 *
 * @param  mixed $input Raw input value.
 * @return float
 */
function folkphotography_sanitize_decimal( $input ) {
    return floatval( $input );
}

// =============================================================================
// HERO IMAGE
// =============================================================================

/**
 * Add a "Use in Hero Rotation" checkbox to the Media Library attachment edit panel.
 *
 * Visible when you click any image in Media → Library. Stored as post meta
 * '_folk_hero' = '1' on the attachment. No categories required.
 *
 * @param  array   $form_fields Existing attachment form fields.
 * @param  WP_Post $post        The attachment post object.
 * @return array
 */
function folkphotography_hero_attachment_field( $form_fields, $post ) {
    $checked = get_post_meta( $post->ID, '_folk_hero', true ) === '1';
    $form_fields['folk_hero'] = array(
        'label' => __( 'Hero Rotation', 'folkphotography' ),
        'input' => 'html',
        'html'  => '<label style="display:flex;align-items:center;gap:6px;">'
                 . '<input type="checkbox" name="attachments[' . $post->ID . '][folk_hero]" value="1"' . checked( $checked, true, false ) . '>'
                 . __( 'Use in homepage hero rotation', 'folkphotography' )
                 . '</label>',
        'helps' => '',
    );
    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'folkphotography_hero_attachment_field', 10, 2 );

/**
 * Save the hero rotation checkbox when an attachment is updated.
 *
 * @param  array $post       Attachment post data (will be saved).
 * @param  array $attachment POST data from the attachment form.
 * @return array
 */
function folkphotography_hero_attachment_field_save( $post, $attachment ) {
    $value = ! empty( $attachment['folk_hero'] ) ? '1' : '';
    update_post_meta( $post['ID'], '_folk_hero', $value );
    return $post;
}
add_filter( 'attachment_fields_to_save', 'folkphotography_hero_attachment_field_save', 10, 2 );

/**
 * Return a random hero image URL from images marked via the Media Library checkbox.
 *
 * Pulls all attachments with _folk_hero = '1', picks one at random.
 * Returns false if none are marked, suppressing the hero section entirely.
 *
 * @return string|false
 */
function folkphotography_get_hero_image() {
    $query = new WP_Query( array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => array(
            array(
                'key'     => '_folk_hero',
                'value'   => '1',
                'compare' => '=',
            ),
        ),
    ) );

    if ( empty( $query->posts ) ) {
        return false;
    }

    $id  = $query->posts[ array_rand( $query->posts ) ];
    $url = wp_get_attachment_image_url( $id, 'hero-fullscreen' );

    return $url ?: false;
}

// =============================================================================
// BODY CLASSES
// =============================================================================

/**
 * Add theme-specific body classes.
 *
 * @param  array $classes Existing body classes.
 * @return array
 */
function folkphotography_body_classes( $classes ) {
    if ( is_front_page() ) {
        $classes[] = 'has-hero';
    }
    return $classes;
}
add_filter( 'body_class', 'folkphotography_body_classes' );

// =============================================================================
// WOOCOMMERCE
// =============================================================================

/**
 * Declare WooCommerce theme support.
 */
function folkphotography_woocommerce_setup() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'folkphotography_woocommerce_setup' );

/**
 * Replace WooCommerce's default content wrappers with theme markup.
 */
function folkphotography_woocommerce_wrapper_start() {
    echo '<main class="site-content woocommerce-content"><div class="content-wrapper">';
}
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
add_action( 'woocommerce_before_main_content', 'folkphotography_woocommerce_wrapper_start', 10 );

function folkphotography_woocommerce_wrapper_end() {
    echo '</div></main>';
}
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_after_main_content', 'folkphotography_woocommerce_wrapper_end', 10 );

/**
 * Show 3 products per row on the shop archive.
 *
 * @return int
 */
function folkphotography_woocommerce_loop_columns() {
    return 3;
}
add_filter( 'loop_shop_columns', 'folkphotography_woocommerce_loop_columns' );

/**
 * Show 12 products per page on the shop archive.
 *
 * @return int
 */
function folkphotography_woocommerce_products_per_page() {
    return 12;
}
add_filter( 'loop_shop_per_page', 'folkphotography_woocommerce_products_per_page' );

// =============================================================================
// CUSTOM POST TYPES & TAXONOMIES
// =============================================================================

/**
 * Register the Portfolio custom post type.
 *
 * Accessible at /portfolio/, supports: title, editor, thumbnail, excerpt, comments.
 * Taxonomies (portfolio_category, portfolio_tag) registered separately below.
 */
function folkphotography_register_portfolio() {
    register_post_type( 'portfolio', array(
        'labels' => array(
            'name'               => __( 'Portfolio',                   'folkphotography' ),
            'singular_name'      => __( 'Portfolio Item',              'folkphotography' ),
            'add_new'            => __( 'Add New',                     'folkphotography' ),
            'add_new_item'       => __( 'Add New Portfolio Item',      'folkphotography' ),
            'edit_item'          => __( 'Edit Portfolio Item',         'folkphotography' ),
            'new_item'           => __( 'New Portfolio Item',          'folkphotography' ),
            'view_item'          => __( 'View Portfolio Item',         'folkphotography' ),
            'search_items'       => __( 'Search Portfolio',            'folkphotography' ),
            'not_found'          => __( 'No portfolio items found',    'folkphotography' ),
            'not_found_in_trash' => __( 'No portfolio items in trash', 'folkphotography' ),
        ),
        'public'       => true,
        'has_archive'  => true,
        'menu_icon'    => 'dashicons-portfolio',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'rewrite'      => array( 'slug' => 'portfolio' ),
        'show_in_rest' => true,
        'taxonomies'   => array( 'portfolio_category', 'portfolio_tag' ),
    ) );
}
add_action( 'init', 'folkphotography_register_portfolio' );

/**
 * Register custom taxonomies.
 *
 * portfolio_category — hierarchical categories for Portfolio items
 * portfolio_tag      — flat tags for Portfolio items
 * photo_subject      — hierarchical subject taxonomy for Posts + Attachments (people, birds, urban…)
 * photo_style        — flat technique taxonomy for Posts + Attachments (long-exposure, film, b&w…)
 *
 * photo_subject and photo_style are intentionally registered even before they
 * are wired up to archive templates — this preserves any data entered early and
 * enables the Block Editor metaboxes for both post types immediately.
 */
function folkphotography_register_taxonomies() {
    register_taxonomy( 'portfolio_category', 'portfolio', array(
        'labels' => array(
            'name'          => __( 'Portfolio Categories', 'folkphotography' ),
            'singular_name' => __( 'Portfolio Category',  'folkphotography' ),
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'portfolio-category' ),
    ) );

    register_taxonomy( 'portfolio_tag', 'portfolio', array(
        'labels' => array(
            'name'          => __( 'Portfolio Tags', 'folkphotography' ),
            'singular_name' => __( 'Portfolio Tag',  'folkphotography' ),
        ),
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'portfolio-tag' ),
    ) );

    register_taxonomy( 'photo_subject', array( 'post', 'attachment' ), array(
        'labels' => array(
            'name'          => __( 'Photo Subjects', 'folkphotography' ),
            'singular_name' => __( 'Photo Subject',  'folkphotography' ),
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'subject' ),
    ) );

    register_taxonomy( 'photo_style', array( 'post', 'attachment' ), array(
        'labels' => array(
            'name'          => __( 'Photo Styles', 'folkphotography' ),
            'singular_name' => __( 'Photo Style',  'folkphotography' ),
        ),
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'style' ),
    ) );
}
add_action( 'init', 'folkphotography_register_taxonomies' );

// =============================================================================
// CUSTOM WIDGETS
// =============================================================================

require_once get_template_directory() . '/inc/widgets.php';
require_once get_template_directory() . '/inc/media-admin.php';
