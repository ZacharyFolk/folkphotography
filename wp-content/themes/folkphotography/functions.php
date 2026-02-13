<?php

define('FOLKPHOTO_VERSION', '1.1.5');

/**
 * Theme setup
 */
function folkphotography_setup()
{
    // Text domain for translations
    load_theme_textdomain('folkphotography', get_template_directory() . '/languages');

    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    add_theme_support('custom-logo');
    add_theme_support('automatic-feed-links');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'folkphotography'),
        'footer' => __('Footer Menu', 'folkphotography'),
    ));

    // Add image sizes
    add_image_size('hero-fullscreen', 2560, 1440, true);
    add_image_size('hero-desktop', 1920, 1080, true);
    add_image_size('portfolio-large', 1200, 1200, false);
    add_image_size('portfolio-medium', 800, 800, false);
    add_image_size('gallery-thumb', 400, 400, true);
    
    // Register category taxonomy for attachments
    // This allows filtering hero images and gallery widgets by category
    register_taxonomy_for_object_type('category', 'attachment');
    register_taxonomy_for_object_type('post_tag', 'attachment');
}
add_action('after_setup_theme', 'folkphotography_setup');

/**
 * Register widget areas
 */
function folkphotography_widgets_init()
{
    // Homepage widgets
    register_sidebar(array(
        'name' => __('Homepage - After Hero', 'folkphotography'),
        'id' => 'homepage-after-hero',
        'description' => __('Appears on homepage after the hero section', 'folkphotography'),
        'before_widget' => '<section id="%1$s" class="widget homepage-widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));

    register_sidebar(array(
        'name' => __('Homepage - Featured Section', 'folkphotography'),
        'id' => 'homepage-featured',
        'description' => __('Featured content area on homepage', 'folkphotography'),
        'before_widget' => '<section id="%1$s" class="widget featured-widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));

    register_sidebar(array(
        'name' => __('Homepage - Gallery Grid', 'folkphotography'),
        'id' => 'homepage-gallery',
        'description' => __('Image gallery section on homepage', 'folkphotography'),
        'before_widget' => '<section id="%1$s" class="widget gallery-widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));

    register_sidebar(array(
        'name' => __('Sidebar', 'folkphotography'),
        'id' => 'sidebar-1',
        'description' => __('Appears on posts and pages with sidebar', 'folkphotography'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'folkphotography_widgets_init');

/**
 * Enqueue scripts and styles
 */
function folkphotography_scripts()
{
    // Google Fonts
    wp_enqueue_style(
        'folkphotography-fonts',
        'https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Poppins:wght@200;300;400;600&family=Rajdhani:wght@300;400;500&display=swap',
        array(),
        null
    );

    // Leaflet CSS (for maps)
    wp_enqueue_style(
        'leaflet',
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
        array(),
        '1.9.4'
    );

    // GLightbox CSS
    wp_enqueue_style(
        'glightbox',
        'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css',
        array(),
        '3.2.0'
    );

    // Main stylesheet
    wp_enqueue_style('folkphotography-style', get_stylesheet_uri(), array(), FOLKPHOTO_VERSION);

    // Leaflet JS (for maps)
    wp_enqueue_script(
        'leaflet',
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
        array(),
        '1.9.4',
        true
    );

    // GLightbox JS
    wp_enqueue_script(
        'glightbox',
        'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js',
        array(),
        '3.2.0',
        true
    );

    // Main JavaScript
    wp_enqueue_script(
        'folkphotography-main',
        get_template_directory_uri() . '/js/main.js',
        array('glightbox'),
        FOLKPHOTO_VERSION,
        true
    );

    // Pass data to JavaScript
    wp_localize_script('folkphotography-main', 'folkphotoData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('folkphoto_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'folkphotography_scripts');

/**
 * Register customizer settings
 */
function folkphotography_customizer($wp_customize)
{
    // Hero Section
    $wp_customize->add_section('folkphotography_hero', array(
        'title' => __('Hero Image Settings', 'folkphotography'),
        'priority' => 30,
    ));

    // Hero category
    $wp_customize->add_setting('hero_category', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('hero_category', array(
        'label' => __('Hero Images Category', 'folkphotography'),
        'description' => __('Select a category to pull random hero images from', 'folkphotography'),
        'section' => 'folkphotography_hero',
        'type' => 'select',
        'choices' => folkphotography_get_category_choices(),
    ));

    // Parallax speed
    $wp_customize->add_setting('parallax_speed', array(
        'default' => 0.5,
        'sanitize_callback' => 'folkphotography_sanitize_decimal',
    ));

    $wp_customize->add_control('parallax_speed', array(
        'label' => __('Parallax Speed', 'folkphotography'),
        'description' => __('Speed of parallax effect (0.1 = slow, 1 = fast)', 'folkphotography'),
        'section' => 'folkphotography_hero',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 0.1,
            'max' => 1,
            'step' => 0.1,
        ),
    ));
}
add_action('customize_register', 'folkphotography_customizer');

/**
 * Get category choices for customizer
 */
function folkphotography_get_category_choices()
{
    $choices = array('' => __('Select a category', 'folkphotography'));
    $categories = get_categories(array('hide_empty' => false));

    foreach ($categories as $category) {
        $choices[$category->term_id] = $category->name;
    }

    return $choices;
}

/**
 * Sanitize decimal values
 */
function folkphotography_sanitize_decimal($input)
{
    return floatval($input);
}

/**
 * Get random hero image from selected category
 */
function folkphotography_get_hero_image()
{
    $hero_category = get_theme_mod('hero_category', '');

    if (empty($hero_category)) {
        return false;
    }

    // Query for random image from category
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'orderby' => 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $hero_category,
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $query->the_post();
        $image_id = get_the_ID();
        $image_url = wp_get_attachment_image_url($image_id, 'hero-fullscreen');
        wp_reset_postdata();
        return $image_url;
    }

    // Fallback: get any random image with location data
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'orderby' => 'rand',
        'meta_query' => array(
            array(
                'key' => '_iwh_has_exif',
                'value' => '1',
                'compare' => '='
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $query->the_post();
        $image_id = get_the_ID();
        $image_url = wp_get_attachment_image_url($image_id, 'hero-fullscreen');
        wp_reset_postdata();
        return $image_url;
    }

    return false;
}

/**
 * Body classes
 */
function folkphotography_body_classes($classes)
{
    if (is_front_page()) {
        $classes[] = 'has-hero';
    }

    return $classes;
}
add_filter('body_class', 'folkphotography_body_classes');

/**
 * WooCommerce Support
 */
function folkphotography_woocommerce_setup()
{
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'folkphotography_woocommerce_setup');

/**
 * WooCommerce wrapper start
 */
function folkphotography_woocommerce_wrapper_start()
{
    echo '<main class="site-content woocommerce-content">';
    echo '<div class="content-wrapper">';
}
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
add_action('woocommerce_before_main_content', 'folkphotography_woocommerce_wrapper_start', 10);

/**
 * WooCommerce wrapper end
 */
function folkphotography_woocommerce_wrapper_end()
{
    echo '</div>';
    echo '</main>';
}
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_after_main_content', 'folkphotography_woocommerce_wrapper_end', 10);

/**
 * Change number of products per row
 */
function folkphotography_woocommerce_loop_columns()
{
    return 3;
}
add_filter('loop_shop_columns', 'folkphotography_woocommerce_loop_columns');

/**
 * Change number of products per page
 */
function folkphotography_woocommerce_products_per_page()
{
    return 12;
}
add_filter('loop_shop_per_page', 'folkphotography_woocommerce_products_per_page');

/**
 * Register Portfolio Custom Post Type
 */
function folkphotography_register_portfolio()
{
    $labels = array(
        'name' => __('Portfolio', 'folkphotography'),
        'singular_name' => __('Portfolio Item', 'folkphotography'),
        'add_new' => __('Add New', 'folkphotography'),
        'add_new_item' => __('Add New Portfolio Item', 'folkphotography'),
        'edit_item' => __('Edit Portfolio Item', 'folkphotography'),
        'new_item' => __('New Portfolio Item', 'folkphotography'),
        'view_item' => __('View Portfolio Item', 'folkphotography'),
        'search_items' => __('Search Portfolio', 'folkphotography'),
        'not_found' => __('No portfolio items found', 'folkphotography'),
        'not_found_in_trash' => __('No portfolio items found in trash', 'folkphotography'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-portfolio',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'rewrite' => array('slug' => 'portfolio'),
        'show_in_rest' => true,
        'taxonomies' => array('portfolio_category', 'portfolio_tag'),
    );

    register_post_type('portfolio', $args);
}
add_action('init', 'folkphotography_register_portfolio');

/**
 * Register Portfolio Taxonomies
 */
function folkphotography_register_taxonomies()
{
    // Portfolio Category
    register_taxonomy('portfolio_category', 'portfolio', array(
        'labels' => array(
            'name' => __('Portfolio Categories', 'folkphotography'),
            'singular_name' => __('Portfolio Category', 'folkphotography'),
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'portfolio-category'),
    ));

    // Portfolio Tag
    register_taxonomy('portfolio_tag', 'portfolio', array(
        'labels' => array(
            'name' => __('Portfolio Tags', 'folkphotography'),
            'singular_name' => __('Portfolio Tag', 'folkphotography'),
        ),
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'portfolio-tag'),
    ));

    // Photo Subject (for regular posts and attachments)
    register_taxonomy('photo_subject', array('post', 'attachment'), array(
        'labels' => array(
            'name' => __('Photo Subjects', 'folkphotography'),
            'singular_name' => __('Photo Subject', 'folkphotography'),
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'subject'),
    ));

    // Photo Style/Technique
    register_taxonomy('photo_style', array('post', 'attachment'), array(
        'labels' => array(
            'name' => __('Photo Styles', 'folkphotography'),
            'singular_name' => __('Photo Style', 'folkphotography'),
        ),
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'style'),
    ));
}
add_action('init', 'folkphotography_register_taxonomies');

/**
 * Load custom widgets
 */
require_once get_template_directory() . '/inc/widgets.php';
