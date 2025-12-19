<?php
if (!defined('ABSPATH')) exit;

// Add meta box
add_action('add_meta_boxes', function () {
    add_meta_box(
        'iwh-location-meta-box',
        'I Was Here Location',
        'iwh_location_meta_box_callback',
        'attachment',
        'side',
        'default'
    );
});

// Meta box HTML
function iwh_location_meta_box_callback($post)
{
    $lat = get_post_meta($post->ID, '_iwh_lat', true) ?: '';
    $lng = get_post_meta($post->ID, '_iwh_lng', true) ?: '';
    $place_name = get_post_meta($post->ID, '_iwh_place_name', true) ?: '';
?>
<label for="iwh-location-search">Search location:</label>
<input type="text" id="iwh-location-search" placeholder="Enter city or place" style="width:100%" />
<button type="button" id="iwh-search-button" class="button">Search</button>

<div id="iwh-map" style="height:250px; margin-top:10px;"></div>

<label for="iwh-lat">Latitude:</label>
<input type="text" id="iwh-lat" name="_iwh_lat" value="<?php echo esc_attr($lat); ?>" style="width:100%" />

<label for="iwh-lng">Longitude:</label>
<input type="text" id="iwh-lng" name="_iwh_lng" value="<?php echo esc_attr($lng); ?>" style="width:100%" />

<label for="iwh-place-name">Place Name (optional):</label>
<input type="text" id="iwh-place-name" name="_iwh_place_name" value="<?php echo esc_attr($place_name); ?>"
    style="width:100%" />
<?php
}


// Enqueue admin scripts
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'post.php') return;
    global $post;
    if (!$post || $post->post_type !== 'attachment') return;

    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet/dist/leaflet.css');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet/dist/leaflet.js', [], null, true);

    wp_enqueue_script(
        'iwh-admin-map',
        plugin_dir_url(__FILE__) . 'js/admin-map.js',
        ['leaflet-js', 'jquery'],
        '0.1',
        true
    );

    wp_localize_script('iwh-admin-map', 'IWH_MAP', [
        'lat' => get_post_meta(get_the_ID(), '_iwh_lat', true) ?: 0,
        'lng' => get_post_meta(get_the_ID(), '_iwh_lng', true) ?: 0,
        'mapboxKey' => get_option('iwh_mapbox_api_key', ''),
    ]);
});

// Save meta
add_action('edit_attachment', function ($post_id) {
    if (isset($_POST['_iwh_lat'])) update_post_meta($post_id, '_iwh_lat', sanitize_text_field($_POST['_iwh_lat']));
    if (isset($_POST['_iwh_lng'])) update_post_meta($post_id, '_iwh_lng', sanitize_text_field($_POST['_iwh_lng']));
    if (isset($_POST['_iwh_place_name'])) update_post_meta($post_id, '_iwh_place_name', sanitize_text_field($_POST['_iwh_place_name']));
});