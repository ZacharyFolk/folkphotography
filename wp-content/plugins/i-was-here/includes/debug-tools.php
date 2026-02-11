<?php
if (! defined('ABSPATH')) exit;

class IWH_Debug_Tools
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_post_iwh_rescan_exif', [$this, 'rescan']);
    }

    public function menu()
    {
        add_management_page(
            'i-was-here Debug',
            'i-was-here',
            'manage_options',
            'iwh-debug',
            [$this, 'render']
        );
    }

    public function render()
    {
?>
<div class="wrap">
    <h1>i-was-here – Debug</h1>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="iwh_rescan_exif">
        <?php wp_nonce_field('iwh_rescan_exif'); ?>
        <?php submit_button('Rescan EXIF for existing attachments'); ?>
    </form>


    <p>Check logs in <code>wp-content/uploads/iwh-logs/dev.log</code></p>
</div>
<?php
    }

    public function rescan()
    {
        if (! current_user_can('manage_options')) {
            wp_die(
                __('You do not have sufficient permissions to access this page.', 'i-was-here'),
                __('Forbidden', 'i-was-here'),
                ['response' => 403]
            );
        }

        check_admin_referer('iwh_rescan_exif');

        $attachments = get_posts([
            'post_type'      => 'attachment',
            'posts_per_page' => 50,
        ]);

        iwh_log('Bulk EXIF rescan started', ['count' => count($attachments)]);

        foreach ($attachments as $a) {
            $file = get_attached_file($a->ID);
            if (! $file) continue;

            iwh_log('Rescanning attachment', $a->ID);
            $exif = IWH_Exif_Reader::read($file);

            // If EXIF data was returned, attempt to backfill attachment meta.
            if (is_array($exif)) {
                // Mark whether this attachment has any EXIF data.
                update_post_meta($a->ID, '_iwh_has_exif', 1);

                // Extract and store camera data using the proper extraction method
                $camera_data = IWH_Exif_Reader::extract_camera_data($exif);
                if ($camera_data) {
                    // Store individual camera settings as post meta
                    foreach ($camera_data as $key => $value) {
                        update_post_meta($a->ID, '_iwh_' . $key, $value);
                    }

                    iwh_log('Camera data extracted during rescan', $camera_data);
                }

                // Extract GPS data if available (reuse logic from attachment-hooks.php)
                if (isset($exif['GPS'])) {
                    $this->process_gps_data($a->ID, $exif['GPS']);
                }
            }
        }

        iwh_log('Bulk EXIF rescan complete');

        wp_redirect(admin_url('tools.php?page=iwh-debug'));
        exit;
    }

    /**
     * Process GPS data from EXIF (same logic as attachment-hooks.php)
     */
    private function process_gps_data($attachment_id, $gps_data)
    {
        // Convert GPS coordinates to decimal format
        $lat = $this->get_gps_coordinate($gps_data, 'Latitude');
        $lng = $this->get_gps_coordinate($gps_data, 'Longitude');

        if ($lat !== null && $lng !== null) {
            update_post_meta($attachment_id, '_iwh_lat', $lat);
            update_post_meta($attachment_id, '_iwh_lng', $lng);

            iwh_log('GPS coordinates extracted during rescan', [
                'attachment_id' => $attachment_id,
                'lat' => $lat,
                'lng' => $lng
            ]);
        }
    }

    /**
     * Extract GPS coordinate from EXIF GPS data
     */
    private function get_gps_coordinate($gps_data, $type)
    {
        $coord_key = 'GPS' . $type;
        $ref_key = 'GPS' . $type . 'Ref';

        if (!isset($gps_data[$coord_key]) || !isset($gps_data[$ref_key])) {
            return null;
        }

        $coord = $gps_data[$coord_key];
        $ref = $gps_data[$ref_key];

        // Convert DMS to decimal
        $degrees = $this->gps_to_num($coord[0]);
        $minutes = $this->gps_to_num($coord[1]);
        $seconds = $this->gps_to_num($coord[2]);

        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        // Apply direction
        if ($ref == 'S' || $ref == 'W') {
            $decimal *= -1;
        }

        return $decimal;
    }

    /**
     * Convert GPS coordinate part to number
     */
    private function gps_to_num($coordPart)
    {
        $parts = explode('/', $coordPart);
        if (count($parts) <= 0) return 0;
        if (count($parts) == 1) return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }
}

new IWH_Debug_Tools();