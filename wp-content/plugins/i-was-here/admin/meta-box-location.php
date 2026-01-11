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

    // Get camera data
    $iso = get_post_meta($post->ID, '_iwh_iso', true);
    $aperture = get_post_meta($post->ID, '_iwh_aperture', true);
    $shutter_speed = get_post_meta($post->ID, '_iwh_shutter_speed', true);
    $focal_length = get_post_meta($post->ID, '_iwh_focal_length', true);
    $camera_make = get_post_meta($post->ID, '_iwh_camera_make', true);
    $camera_model = get_post_meta($post->ID, '_iwh_camera_model', true);
    $lens = get_post_meta($post->ID, '_iwh_lens', true);
    $date_taken = get_post_meta($post->ID, '_iwh_date_taken', true);
?>

<!-- Camera Data Section -->
<?php if ($iso || $aperture || $shutter_speed || $focal_length): ?>
<div style="background: #f0f0f1; padding: 10px; margin-bottom: 15px; border-radius: 3px;">
    <h4 style="margin-top: 0;">Camera Settings</h4>
    <table style="width: 100%; font-size: 12px;">
        <?php if ($camera_make || $camera_model): ?>
        <tr>
            <td style="padding: 3px 0;"><strong>Camera:</strong></td>
            <td><?php echo esc_html(trim($camera_make . ' ' . $camera_model)); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($lens): ?>
        <tr>
            <td style="padding: 3px 0;"><strong>Lens:</strong></td>
            <td><?php echo esc_html($lens); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($iso): ?>
        <tr>
            <td style="padding: 3px 0; width: 35%;"><strong>ISO:</strong></td>
            <td><?php echo esc_html($iso); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($aperture): ?>
        <tr>
            <td style="padding: 3px 0;"><strong>Aperture:</strong></td>
            <td>ƒ/<?php echo esc_html($aperture); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($shutter_speed): ?>
        <tr>
            <td style="padding: 3px 0;"><strong>Shutter Speed:</strong></td>
            <td><?php echo esc_html($shutter_speed); ?>s</td>
        </tr>
        <?php endif; ?>
        <?php if ($focal_length): ?>
        <tr>
            <td style="padding: 3px 0;"><strong>Focal Length:</strong></td>
            <td><?php echo esc_html($focal_length); ?>mm</td>
        </tr>
        <?php endif; ?>
        <?php if ($date_taken): ?>
        <tr>
            <td style="padding: 3px 0;"><strong>Date Taken:</strong></td>
            <td><?php echo esc_html($date_taken); ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<?php endif; ?>

<!-- Location Section -->
<h4>Location</h4>
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