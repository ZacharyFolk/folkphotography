<?php
if (!defined('ABSPATH')) exit;

// Add meta box
add_action('add_meta_boxes', function () {
    add_meta_box(
        'iwh-location-meta-box',
        __( 'I Was Here Location', 'i-was-here' ),
        'iwh_location_meta_box_callback',
        'attachment',
        'side',
        'default'
    );
});

// Meta box HTML
function iwh_location_meta_box_callback($post)
{
    // Add nonce field for security
    wp_nonce_field('iwh_location_meta_box', 'iwh_location_meta_box_nonce');
    
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

<?php if ($iso || $aperture || $shutter_speed || $focal_length): ?>
<div style="background: #f0f0f1; padding: 10px; margin-bottom: 15px; border-radius: 3px;">
    <h4 style="margin-top: 0;"><?php esc_html_e( 'Camera Settings', 'i-was-here' ); ?></h4>
    <table style="width: 100%; font-size: 12px;">
        <?php if ($camera_make || $camera_model): ?>
        <tr>
            <td style="padding: 3px 0;"><strong><?php esc_html_e( 'Camera:', 'i-was-here' ); ?></strong></td>
            <td><?php echo esc_html(trim($camera_make . ' ' . $camera_model)); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($lens): ?>
        <tr>
            <td style="padding: 3px 0;"><strong><?php esc_html_e( 'Lens:', 'i-was-here' ); ?></strong></td>
            <td><?php echo esc_html($lens); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($iso): ?>
        <tr>
            <td style="padding: 3px 0; width: 35%;"><strong><?php esc_html_e( 'ISO:', 'i-was-here' ); ?></strong></td>
            <td><?php echo esc_html($iso); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($aperture): ?>
        <tr>
            <td style="padding: 3px 0;"><strong><?php esc_html_e( 'Aperture:', 'i-was-here' ); ?></strong></td>
            <td>ƒ/<?php echo esc_html($aperture); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($shutter_speed): ?>
        <tr>
            <td style="padding: 3px 0;"><strong><?php esc_html_e( 'Shutter Speed:', 'i-was-here' ); ?></strong></td>
            <td><?php echo esc_html($shutter_speed); ?>s</td>
        </tr>
        <?php endif; ?>
        <?php if ($focal_length): ?>
        <tr>
            <td style="padding: 3px 0;"><strong><?php esc_html_e( 'Focal Length:', 'i-was-here' ); ?></strong></td>
            <td><?php echo esc_html($focal_length); ?>mm</td>
        </tr>
        <?php endif; ?>
        <?php if ($date_taken): ?>
        <tr>
            <td style="padding: 3px 0;"><strong><?php esc_html_e( 'Date Taken:', 'i-was-here' ); ?></strong></td>
            <td><?php echo esc_html($date_taken); ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<?php endif; ?>

<h4><?php esc_html_e( 'Location', 'i-was-here' ); ?></h4>
<label for="iwh-location-search"><?php esc_html_e( 'Search location:', 'i-was-here' ); ?></label>
<input type="text" id="iwh-location-search" placeholder="<?php echo esc_attr__( 'Enter city or place', 'i-was-here' ); ?>" style="width:100%" />
<button type="button" id="iwh-search-button" class="button"><?php esc_html_e( 'Search', 'i-was-here' ); ?></button>

<div id="iwh-map" style="height:250px; margin-top:10px;"></div>

<label for="iwh-lat"><?php esc_html_e( 'Latitude:', 'i-was-here' ); ?></label>
<input type="text" id="iwh-lat" name="_iwh_lat" value="<?php echo esc_attr($lat); ?>" style="width:100%" />

<label for="iwh-lng"><?php esc_html_e( 'Longitude:', 'i-was-here' ); ?></label>
<input type="text" id="iwh-lng" name="_iwh_lng" value="<?php echo esc_attr($lng); ?>" style="width:100%" />

<label for="iwh-place-name"><?php esc_html_e( 'Place Name (optional):', 'i-was-here' ); ?></label>
<input type="text" id="iwh-place-name" name="_iwh_place_name" value="<?php echo esc_attr($place_name); ?>"
    style="width:100%" />
<?php
}


// Enqueue admin scripts
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'post.php') return;
    global $post;
    if (!$post || $post->post_type !== 'attachment') return;

    // Use consistent Leaflet handle and pinned version
    if (!wp_script_is('leaflet', 'registered')) {
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
        wp_enqueue_style('leaflet');
        wp_enqueue_script('leaflet');
    }

    wp_enqueue_script(
        'iwh-admin-map',
        plugin_dir_url(__FILE__) . 'js/admin-map.js',
        ['leaflet', 'jquery'], // Use 'leaflet' handle (same as theme)
        '0.1',
        true
    );

    wp_localize_script('iwh-admin-map', 'IWH_MAP', [
        'lat' => get_post_meta($post->ID, '_iwh_lat', true) ?: 0,
        'lng' => get_post_meta($post->ID, '_iwh_lng', true) ?: 0,
    ]);
});

// Save meta with security checks
add_action('edit_attachment', function ($post_id) {
    // Verify nonce
    if (!isset($_POST['iwh_location_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['iwh_location_meta_box_nonce'], 'iwh_location_meta_box')) {
        return;
    }
    
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save latitude
    if (isset($_POST['_iwh_lat'])) {
        $lat = sanitize_text_field($_POST['_iwh_lat']);
        // Validate latitude is a number between -90 and 90
        if (is_numeric($lat) && $lat >= -90 && $lat <= 90) {
            update_post_meta($post_id, '_iwh_lat', $lat);
        } elseif (empty($lat)) {
            delete_post_meta($post_id, '_iwh_lat');
        }
    }
    
    // Save longitude
    if (isset($_POST['_iwh_lng'])) {
        $lng = sanitize_text_field($_POST['_iwh_lng']);
        // Validate longitude is a number between -180 and 180
        if (is_numeric($lng) && $lng >= -180 && $lng <= 180) {
            update_post_meta($post_id, '_iwh_lng', $lng);
        } elseif (empty($lng)) {
            delete_post_meta($post_id, '_iwh_lng');
        }
    }
    
    // Save place name
    if (isset($_POST['_iwh_place_name'])) {
        $place_name = sanitize_text_field($_POST['_iwh_place_name']);
        if (!empty($place_name)) {
            update_post_meta($post_id, '_iwh_place_name', $place_name);
        } else {
            delete_post_meta($post_id, '_iwh_place_name');
        }
    }
});