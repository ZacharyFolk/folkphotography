<?php
/**
 * i-was-here Tools Page
 *
 * Admin page for plugin management:
 * - Batched AJAX EXIF rescan with progress bar and resume support
 * - Plugin status and stats
 *
 * @package i-was-here
 * @since 0.6.0
 */

if (! defined('ABSPATH')) exit;

class IWH_Tools_Page
{
    const BATCH_SIZE  = 20;
    const OPTION_SCAN = 'iwh_scan_state';

    public function __construct()
    {
        add_action('admin_menu',               [$this, 'register_menu']);
        add_action('wp_ajax_iwh_rescan_batch', [$this, 'handle_batch']);
    }

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

    public function render()
    {
        $stats      = $this->get_stats();
        $scan_state = get_option(self::OPTION_SCAN, []);
        $is_stalled = ! empty($scan_state['status']) && $scan_state['status'] === 'running';
        $last_scan  = get_option('iwh_last_scan_time', '');
        $last_count = get_option('iwh_last_scan_count', 0);
        $nonce      = wp_create_nonce('iwh_rescan_batch');
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('i-was-here Tools', 'i-was-here'); ?></h1>

            <!-- Library Stats -->
            <div class="card" style="max-width:800px;margin-bottom:20px;">
                <h2><?php esc_html_e('Library Status', 'i-was-here'); ?></h2>
                <table class="widefat striped" style="max-width:500px;">
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
                                if ($unscanned > 0) {
                                    echo ' <span style="color:#d63638;">&#9888;</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php if ($last_scan) : ?>
                    <p class="description" style="margin-top:10px;">
                        <?php printf(
                            esc_html__('Last scan: %1$s (%2$d attachments processed)', 'i-was-here'),
                            esc_html($last_scan),
                            absint($last_count)
                        ); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Rescan Tool -->
            <div class="card" style="max-width:800px;margin-bottom:20px;">
                <h2><?php esc_html_e('Rescan EXIF Data', 'i-was-here'); ?></h2>
                <p><?php esc_html_e(
                    'Scan all image attachments to extract EXIF metadata (camera settings, GPS coordinates). ' .
                    'Useful for images uploaded before the plugin was activated, or if data was not extracted properly.',
                    'i-was-here'
                ); ?></p>
                <p class="description"><?php esc_html_e(
                    'Scanning runs in small batches so it will not time out on large libraries.',
                    'i-was-here'
                ); ?></p>

                <?php if ($is_stalled) : ?>
                <div class="notice notice-warning inline" style="margin:12px 0;">
                    <p>
                        <?php printf(
                            esc_html__('A previous scan was interrupted at %1$d / %2$d images.', 'i-was-here'),
                            absint($scan_state['processed'] ?? 0),
                            absint($scan_state['total'] ?? 0)
                        ); ?>
                        <button type="button" id="iwh-resume-btn" class="button" style="margin-left:8px;">
                            <?php esc_html_e('Resume', 'i-was-here'); ?>
                        </button>
                    </p>
                </div>
                <?php endif; ?>

                <p>
                    <label>
                        <input type="checkbox" id="iwh-overwrite" value="1">
                        <?php esc_html_e('Overwrite existing EXIF data (re-extract everything)', 'i-was-here'); ?>
                    </label>
                </p>
                <p>
                    <button type="button" id="iwh-scan-btn" class="button button-primary">
                        <?php printf(
                            esc_html__('Scan All Images (%d)', 'i-was-here'),
                            $stats['total_images']
                        ); ?>
                    </button>
                </p>

                <div id="iwh-progress-wrap" style="display:none;margin-top:16px;">
                    <div style="background:#e0e0e0;border-radius:3px;height:20px;overflow:hidden;max-width:400px;">
                        <div id="iwh-progress-bar" style="background:#2271b1;height:100%;width:0%;transition:width .3s;"></div>
                    </div>
                    <p id="iwh-progress-text" style="margin:6px 0 2px;"></p>
                    <p id="iwh-progress-status" style="margin:2px 0;font-style:italic;color:#555;"></p>
                </div>
            </div>

            <?php if (defined('WP_DEBUG') && WP_DEBUG) : ?>
            <div class="card" style="max-width:800px;">
                <h2><?php esc_html_e('Debug Info', 'i-was-here'); ?></h2>
                <table class="widefat striped" style="max-width:500px;">
                    <tbody>
                        <tr>
                            <td><strong><?php esc_html_e('Plugin Version', 'i-was-here'); ?></strong></td>
                            <td><?php echo esc_html(IWH_VERSION); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('EXIF Extension', 'i-was-here'); ?></strong></td>
                            <td><?php echo function_exists('exif_read_data')
                                ? '&#9989; ' . esc_html__('Loaded', 'i-was-here')
                                : '&#10060; ' . esc_html__('Not available', 'i-was-here'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Logging', 'i-was-here'); ?></strong></td>
                            <td><?php echo IWH_LOG_ENABLED
                                ? '&#9989; ' . esc_html__('Enabled', 'i-was-here')
                                : esc_html__('Disabled', 'i-was-here'); ?></td>
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

        <script>
        (function ($) {
            var cfg = {
                ajaxUrl:  <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>,
                nonce:    <?php echo wp_json_encode($nonce); ?>,
                scanning: <?php echo wp_json_encode(__('Scanning…', 'i-was-here')); ?>,
                complete: <?php echo wp_json_encode(__('Scan complete!', 'i-was-here')); ?>,
                error:    <?php echo wp_json_encode(__('Scan failed. Please try again.', 'i-was-here')); ?>,
                resume:   <?php echo $is_stalled ? wp_json_encode($scan_state) : 'null'; ?>,
            };

            var state = { running: false, offset: 0, processed: 0, total: 0, overwrite: false };

            function start(overwrite, initOffset, initProcessed, initTotal) {
                state.running   = true;
                state.overwrite = !! overwrite;
                state.offset    = initOffset    || 0;
                state.processed = initProcessed || 0;
                state.total     = initTotal     || 0;

                $('#iwh-scan-btn, #iwh-resume-btn').prop('disabled', true);
                $('#iwh-scan-btn').text(cfg.scanning);
                $('#iwh-progress-wrap').show();
                $('#iwh-progress-status').text('').css('color', '');

                runBatch();
            }

            function runBatch() {
                $.post(cfg.ajaxUrl, {
                    action:    'iwh_rescan_batch',
                    nonce:     cfg.nonce,
                    offset:    state.offset,
                    overwrite: state.overwrite ? 1 : 0,
                })
                .done(function (response) {
                    if (! response.success) {
                        showError(response.data || cfg.error);
                        return;
                    }
                    var d = response.data;
                    if (! state.total) { state.total = d.total; }
                    state.processed = d.total_processed;
                    state.offset    = d.next_offset;
                    updateBar(state.processed, state.total);
                    d.done ? finish() : runBatch();
                })
                .fail(function () { showError(cfg.error); });
            }

            function updateBar(done, total) {
                var pct = total > 0 ? Math.round(done / total * 100) : 0;
                $('#iwh-progress-bar').css('width', pct + '%');
                $('#iwh-progress-text').text(done + ' / ' + total + ' (' + pct + '%)');
            }

            function finish() {
                state.running = false;
                updateBar(state.total, state.total);
                $('#iwh-progress-status').text(cfg.complete);
                $('#iwh-scan-btn').prop('disabled', false);
                setTimeout(function () { location.reload(); }, 1500);
            }

            function showError(msg) {
                state.running = false;
                $('#iwh-progress-status').text(msg).css('color', '#d63638');
                $('#iwh-scan-btn, #iwh-resume-btn').prop('disabled', false);
            }

            $('#iwh-scan-btn').on('click', function () {
                start($('#iwh-overwrite').is(':checked'));
            });

            // Resume an interrupted scan if one exists.
            if (cfg.resume && cfg.resume.status === 'running') {
                $('#iwh-resume-btn').on('click', function () {
                    var rs = cfg.resume;
                    // Overwrite mode uses a numeric offset; non-overwrite always starts at 0
                    // because processed items fall off the unscanned query automatically.
                    var resumeOffset = rs.overwrite ? (rs.processed || 0) : 0;
                    start(rs.overwrite, resumeOffset, rs.processed || 0, rs.total || 0);
                });
            }
        }(jQuery));
        </script>
        <?php
    }

    /**
     * AJAX handler — process one batch and return progress.
     */
    public function handle_batch()
    {
        check_ajax_referer('iwh_rescan_batch', 'nonce');

        if (! current_user_can('manage_options')) {
            wp_send_json_error(__('Unauthorized', 'i-was-here'), 403);
        }

        $overwrite = ! empty($_POST['overwrite']) && '1' === $_POST['overwrite'];
        $offset    = absint($_POST['offset'] ?? 0);

        // First call: record total and initialise persisted state.
        if ($offset === 0 && ! $overwrite) {
            $total = $this->count_scannable(false);
            update_option(self::OPTION_SCAN, [
                'status'    => 'running',
                'total'     => $total,
                'processed' => 0,
                'overwrite' => false,
                'started'   => time(),
            ]);
        } elseif ($offset === 0 && $overwrite) {
            $total = $this->count_scannable(true);
            update_option(self::OPTION_SCAN, [
                'status'    => 'running',
                'total'     => $total,
                'processed' => 0,
                'overwrite' => true,
                'started'   => time(),
            ]);
        } else {
            $saved = get_option(self::OPTION_SCAN, []);
            $total = $saved['total'] ?? 0;
        }

        $ids   = $this->get_batch($overwrite, $offset);
        $count = 0;

        foreach ($ids as $attachment_id) {
            $file = get_attached_file($attachment_id);
            if (! $file || ! file_exists($file)) {
                continue;
            }

            $exif = IWH_Exif_Reader::read($file);

            if (is_array($exif)) {
                update_post_meta($attachment_id, '_iwh_has_exif', 1);

                $camera_data = IWH_Exif_Reader::extract_camera_data($exif);
                if ($camera_data) {
                    foreach ($camera_data as $key => $value) {
                        update_post_meta($attachment_id, '_iwh_' . $key, $value);
                    }
                }

                if (isset($exif['GPS'])) {
                    $lat = $this->get_gps_coordinate($exif['GPS'], 'Latitude');
                    $lng = $this->get_gps_coordinate($exif['GPS'], 'Longitude');
                    if ($lat !== null && $lng !== null) {
                        $existing = get_post_meta($attachment_id, '_iwh_lat', true);
                        if ($overwrite || empty($existing)) {
                            update_post_meta($attachment_id, '_iwh_lat', $lat);
                            update_post_meta($attachment_id, '_iwh_lng', $lng);
                        }
                    }
                }
            } else {
                update_post_meta($attachment_id, '_iwh_has_exif', 0);
            }

            $count++;
        }

        // Persist progress so the page can show a resume option if interrupted.
        $saved              = get_option(self::OPTION_SCAN, []);
        $total_processed    = ($saved['processed'] ?? 0) + $count;
        $done               = count($ids) < self::BATCH_SIZE;
        $saved['processed'] = $total_processed;

        if ($done) {
            $saved['status'] = 'done';
            update_option('iwh_last_scan_time',  current_time('mysql'));
            update_option('iwh_last_scan_count', $total_processed);
        }

        update_option(self::OPTION_SCAN, $saved);
        iwh_log('Batch processed', ['offset' => $offset, 'count' => $count, 'done' => $done]);

        wp_send_json_success([
            'processed_in_batch' => $count,
            'total_processed'    => $total_processed,
            'total'              => $total ?? ($saved['total'] ?? 0),
            // Non-overwrite: always offset 0 — processed items drop off the unscanned query.
            // Overwrite: advance offset by the number processed in this batch.
            'next_offset'        => $overwrite ? $offset + $count : 0,
            'done'               => $done,
        ]);
    }

    /**
     * Count images eligible for scanning.
     */
    private function count_scannable(bool $overwrite): int
    {
        $args = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'no_found_rows'  => false,
        ];
        if (! $overwrite) {
            $args['meta_query'] = [
                ['key' => '_iwh_has_exif', 'compare' => 'NOT EXISTS'],
            ];
        }
        $q = new WP_Query($args);
        return (int) $q->found_posts;
    }

    /**
     * Fetch one page of attachment IDs to scan.
     */
    private function get_batch(bool $overwrite, int $offset): array
    {
        $args = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => self::BATCH_SIZE,
            'fields'         => 'ids',
            'orderby'        => 'ID',
            'order'          => 'ASC',
        ];
        if ($overwrite) {
            $args['offset'] = $offset;
        } else {
            $args['meta_query'] = [
                ['key' => '_iwh_has_exif', 'compare' => 'NOT EXISTS'],
            ];
        }
        return get_posts($args);
    }

    private function get_stats(): array
    {
        global $wpdb;

        $total_images = (int) $wpdb->get_var(
            "SELECT COUNT(ID) FROM {$wpdb->posts}
             WHERE post_type = 'attachment'
             AND post_mime_type LIKE 'image/%'
             AND post_status = 'inherit'"
        );

        $with_exif = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta}
             WHERE meta_key = '_iwh_has_exif' AND meta_value = '1'"
        );

        $with_gps = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT lat.post_id)
             FROM {$wpdb->postmeta} lat
             INNER JOIN {$wpdb->postmeta} lng ON lat.post_id = lng.post_id
             WHERE lat.meta_key = '_iwh_lat' AND lat.meta_value != ''
             AND lng.meta_key = '_iwh_lng' AND lng.meta_value != ''"
        );

        $with_camera = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta}
             WHERE meta_key = '_iwh_camera_model' AND meta_value != ''"
        );

        return [
            'total_images' => $total_images,
            'with_exif'    => $with_exif,
            'with_gps'     => $with_gps,
            'with_camera'  => $with_camera,
        ];
    }

    private function get_gps_coordinate(array $gps_data, string $type): ?float
    {
        $coord_key = 'GPS' . $type;
        $ref_key   = 'GPS' . $type . 'Ref';

        if (! isset($gps_data[$coord_key], $gps_data[$ref_key])) {
            return null;
        }

        $coord   = $gps_data[$coord_key];
        $decimal = $this->gps_to_num($coord[0])
                 + $this->gps_to_num($coord[1]) / 60
                 + $this->gps_to_num($coord[2]) / 3600;

        return ($gps_data[$ref_key] === 'S' || $gps_data[$ref_key] === 'W') ? -$decimal : $decimal;
    }

    private function gps_to_num(string $coordPart): float
    {
        $parts = explode('/', $coordPart);
        return count($parts) === 1
            ? (float) $parts[0]
            : (float) $parts[0] / (float) $parts[1];
    }
}

new IWH_Tools_Page();
