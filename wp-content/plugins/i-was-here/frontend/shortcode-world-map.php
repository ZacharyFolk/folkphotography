<?php
if (!defined('ABSPATH')) exit;

/**
 * Shortcode: [iwh_world_map height="600px"]
 */
add_shortcode('iwh_world_map', function ($atts) {

    $atts = shortcode_atts([
        'height' => '600px',
    ], $atts);

    // Query attachments with lat/lng
    $attachments = get_posts([
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => '_iwh_lat',
                'compare' => 'EXISTS',
            ],
            [
                'key'     => '_iwh_lng',
                'compare' => 'EXISTS',
            ],
        ],
    ]);

    $pins = [];

    foreach ($attachments as $attachment) {
        $lat = get_post_meta($attachment->ID, '_iwh_lat', true);
        $lng = get_post_meta($attachment->ID, '_iwh_lng', true);

        if (!$lat || !$lng) continue;

        $link = $attachment->post_parent
            ? get_permalink($attachment->post_parent)
            : get_attachment_link($attachment->ID);

        $pins[] = [
            'lat'   => (float) $lat,
            'lng'   => (float) $lng,
            'title' => esc_html(get_the_title($attachment->ID)),
            'thumb' => wp_get_attachment_image_url($attachment->ID, 'thumbnail'),
            'link'  => $link,
        ];
    }

    // Use theme's Leaflet if available, otherwise enqueue our own
    if (!wp_script_is('leaflet', 'registered')) {
        // Enqueue pinned version if theme doesn't provide Leaflet
        wp_enqueue_style(
            'leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            [],
            '1.9.4'
        );
        wp_enqueue_script(
            'leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            [],
            '1.9.4',
            true
        );
    } else {
        // Theme provides Leaflet, just ensure it's enqueued
        wp_enqueue_style('leaflet');
        wp_enqueue_script('leaflet');
    }

    // Enqueue our frontend map script (depends on leaflet)
    wp_enqueue_script(
        'iwh-frontend-map',
        plugin_dir_url(__FILE__) . 'js/frontend-map.js',
        ['leaflet'], // Use 'leaflet' handle (same as theme)
        '0.1',
        true
    );

    wp_localize_script('iwh-frontend-map', 'IWH_FRONTEND', [
        'pins' => $pins,
    ]);

    ob_start();
?>
<div class="iwh-world-map" style="height: <?php echo esc_attr($atts['height']); ?>; width:100%;" data-iwh-map></div>
<?php
    return ob_get_clean();
});