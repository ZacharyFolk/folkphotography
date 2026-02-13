<?php
/**
 * i-was-here Tools Page
 *
 * Single consolidated admin page for plugin management:
 * - EXIF rescan for existing attachments
 * - Plugin status and stats
 *
 * @package i-was-here
 * @since 0.6.0
 */

if (! defined('ABSPATH')) exit;

class IWH_Tools_Page
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_post_iwh_rescan_exif', [$this, 'handle_rescan']);
    }

    /**
     * Register the single tools page under Tools menu
     */
    public function register_menu()
    {
        add_management_page(
            __('i-was-here Tools', 'i-was-here'),
            __('i-was-here', 'i-was-here'),
            'manage_options',
            'iwh-tools',
            [$this, 'render']
        );
    }

    /**
     * Render the tools page
     */
    public function render()
    {
        $stats = $this->get_stats();
        $last_scan = get_option('iwh_last_scan_time', '');
        $last_scan_count = get_option('iwh_last_scan_count', 0);
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('i-was-here Tools', 'i-was-here'); ?></h1>

            <?php if (isset($_GET['scan_complete'])) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php printf(
                        esc_html__('Scan complete! Processed %d attachments.', 'i-was-here'),
                        absint($_GET['scan_count'])
                    ); ?></p>
                </div>
            <?php endif; ?>

            <!-- Library Stats -->
            <div class="card" style="max-width: 800px; margin-bottom: 20px;">
                <h2><?php esc_html_e('Library Status', 'i-was-here'); ?></h2>
                <table class="widefat striped" style="max-width: 500px;">
                    <tbody>
                        <tr>
                            <td><strong><?php esc_html_e('Total Image Attachments', 'i-was-here'); ?></strong></td>
                            <td><?php echo esc_html(number_format($stats['total_images'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('With EXIF Data', 'i-was-here'); ?></strong></td>
                            <td><?php echo esc_html(number_format($stats['with_exif'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('With GPS Coordinates', 'i-was-here'); ?></strong></td>
                            <td><?php echo esc_html(number_format($stats['with_gps'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('With Camera Data', 'i-was-here'); ?></strong></td>
                            <td><?php echo esc_html(number_format($stats['with_camera'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Not Yet Scanned', 'i-was-here'); ?></strong></td>
                            <td>
                                <?php
                                $unscanned = $stats['total_images'] - $stats['with_exif'];
                                echo esc_html(number_format(max(0, $unscanned)));
                                if ($unscanned > 0) :
                                    echo ' <span style="color: #d63638;">⚠</span>';
                                endif;
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php if ($last_scan) : ?>
                    <p class="description" style="margin-top: 10px;">
                        <?php printf(
                            esc_html__('Last scan: %1$s (%2$d attachments processed)', 'i-was-here'),
                            esc_html($last_scan),
                            absint($last_scan_count)
                        ); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Rescan Tool -->
            <div class="card" style="max-width: 800px; margin-bottom: 20px;">
                <h2><?php esc_html_e('Rescan EXIF Data', 'i-was-here'); ?></h2>
                <p><?php esc_html_e(
                    'Scan all image attachments to extract EXIF metadata (camera settings, GPS coordinates). ' .
                    'This is useful for images uploaded before the plugin was activated, or if data was not extracted properly.',
                    'i-was-here'
                ); ?></p>
                <p class="description"><?php esc_html_e(
                    'Note: This will not overwrite manually entered location data. Only missing metadata will be filled in.',
                    'i-was-here'
                ); ?></p>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="iwh_rescan_exif">
                    <?php wp_nonce_field('iwh_rescan_exif'); ?>

                    <p>
                        <label>
                            <input type="checkbox" name="iwh_overwrite" value="1">
                            <?php esc_html_e('Overwrite existing EXIF data (re-extract everything)', 'i-was-here'); ?>
                        </label>
                    </p>

                    <?php submit_button(
                        sprintf(
                            __('Scan All Images (%d)', 'i-was-here'),
                            $stats['total_images']
                        ),
                        'primary',
                        'submit',
                        false
                    ); ?>
                </form>
            </div>

            <!-- Debug Info -->
            <?php if (defined('WP_DEBUG') && WP_DEBUG) : ?>
            <div class="card" style="max-width: 800px;">
                <h2><?php esc_html_e('Debug Info', 'i-was-here'); ?></h2>
                <table class="widefat striped" style="max-width: 500px;">
                    <tbody>
                        <tr>
                            <td><strong><?php esc_html_e('Plugin Version', 'i-was-here'); ?></strong></td>
                            <td><?php echo esc_html(IWH_VERSION); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('EXIF Extension', 'i-was-here'); ?></strong></td>
                            <td><?php echo function_exists('exif_read_data') ? '✅ ' . esc_html__('Loaded', 'i-was-here') : '❌ ' . esc_html__('Not available', 'i-was-here'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Logging', 'i-was-here'); ?></strong></td>
                            <td><?php echo IWH_LOG_ENABLED ? '✅ ' . esc_html__('Enabled', 'i-was-here') : esc_html__('Disabled', 'i-was-here'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Log File', 'i-was-here'); ?></strong></td>
                            <td><code>wp-content/uploads/iwh-logs/dev.log</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </div>
        <?php
    }

    /**
     * Get library statistics
     */
    private function get_stats()
    {
        global $wpdb;

        $total_images = $wpdb->get_var(
            "SELECT COUNT(ID) FROM {$wpdb->posts} 
             WHERE post_type = 'attachment' 
             AND post_mime_type LIKE 'image/%' 
             AND post_status = 'inherit'"
        );

        $with_exif = $wpdb->get_var(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} 
             WHERE meta_key = '_iwh_has_exif' AND meta_value = '1'"
        );

        $with_gps = $wpdb->get_var(
            "SELECT COUNT(DISTINCT lat.post_id) 
             FROM {$wpdb->postmeta} lat
             INNER JOIN {$wpdb->postmeta} lng ON lat.post_id = lng.post_id
             WHERE lat.meta_key = '_iwh_lat' AND lat.meta_value != ''
             AND lng.meta_key = '_iwh_lng' AND lng.meta_value != ''"
        );

        $with_camera = $wpdb->get_var(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} 
             WHERE meta_key = '_iwh_camera_model' AND meta_value != ''"
        );

        return [
            'total_images' => (int) $total_images,
            'with_exif'    => (int) $with_exif,
            'with_gps'     => (int) $with_gps,
            'with_camera'  => (int) $with_camera,
        ];
    }

    /**
     * Handle the rescan form submission
     */
    public function handle_rescan()
    {
        if (! current_user_can('manage_options')) {
            wp_die(
                __('You do not have sufficient permissions to access this page.', 'i-was-here'),
                __('Forbidden', 'i-was-here'),
                ['response' => 403]
            );
        }

        check_admin_referer('iwh_rescan_exif');

        $overwrite = isset($_POST['iwh_overwrite']) && $_POST['iwh_overwrite'] === '1';

        // Get ALL image attachments (no arbitrary limit)
        $args = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];

        // If not overwriting, only get attachments that haven't been scanned
        if (! $overwrite) {
            $args['meta_query'] = [
                [
                    'key'     => '_iwh_has_exif',
                    'compare' => 'NOT EXISTS',
                ],
            ];
        }

        $attachment_ids = get_posts($args);
        $count = count($attachment_ids);

        iwh_log('Bulk EXIF rescan started', ['count' => $count, 'overwrite' => $overwrite]);

        foreach ($attachment_ids as $attachment_id) {
            $file = get_attached_file($attachment_id);
            if (! $file || ! file_exists($file)) {
                continue;
            }

            $exif = IWH_Exif_Reader::read($file);

            if (is_array($exif)) {
                update_post_meta($attachment_id, '_iwh_has_exif', 1);

                // Extract and store camera data
                $camera_data = IWH_Exif_Reader::extract_camera_data($exif);
                if ($camera_data) {
                    foreach ($camera_data as $key => $value) {
                        update_post_meta($attachment_id, '_iwh_' . $key, $value);
                    }
                }

                // Extract GPS data if available
                if (isset($exif['GPS'])) {
                    $lat = $this->get_gps_coordinate($exif['GPS'], 'Latitude');
                    $lng = $this->get_gps_coordinate($exif['GPS'], 'Longitude');

                    if ($lat !== null && $lng !== null) {
                        // Only overwrite GPS if option is set or no existing data
                        $existing_lat = get_post_meta($attachment_id, '_iwh_lat', true);
                        if ($overwrite || empty($existing_lat)) {
                            update_post_meta($attachment_id, '_iwh_lat', $lat);
                            update_post_meta($attachment_id, '_iwh_lng', $lng);
                        }
                    }
                }
            } else {
                // Mark as scanned even if no EXIF found (so we don't rescan it again)
                update_post_meta($attachment_id, '_iwh_has_exif', 0);
            }
        }

        // Store scan metadata
        update_option('iwh_last_scan_time', current_time('mysql'));
        update_option('iwh_last_scan_count', $count);

        iwh_log('Bulk EXIF rescan complete', ['processed' => $count]);

        wp_redirect(add_query_arg([
            'page'          => 'iwh-tools',
            'scan_complete' => 1,
            'scan_count'    => $count,
        ], admin_url('tools.php')));
        exit;
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

        $degrees = $this->gps_to_num($coord[0]);
        $minutes = $this->gps_to_num($coord[1]);
        $seconds = $this->gps_to_num($coord[2]);

        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

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

new IWH_Tools_Page();
