<?php

/**
 * Plugin Name: i-was-here
 * Description: Automatically extracts EXIF metadata and GPS coordinates from uploaded images. Provides location maps, camera stats, and bulk scanning tools.
 * Version: 0.6.0
 * Author: Zachary Folk
 * Text Domain: i-was-here
 */

if (! defined('ABSPATH')) exit;

// --------------------------------------------------
// CONSTANTS
// --------------------------------------------------

define('IWH_VERSION', '0.6.0');
define('IWH_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Logging is disabled by default unless WP_DEBUG is explicitly enabled.
$iwh_log_default_enabled = (defined('WP_DEBUG') && WP_DEBUG);
if (! defined('IWH_LOG_ENABLED')) {
    define(
        'IWH_LOG_ENABLED',
        function_exists('apply_filters')
            ? (bool) apply_filters('iwh_log_enabled', $iwh_log_default_enabled)
            : $iwh_log_default_enabled
    );
}
// --------------------------------------------------
// INCLUDES
// --------------------------------------------------

require_once IWH_PLUGIN_DIR . 'includes/logger.php';
require_once IWH_PLUGIN_DIR . 'includes/exif-reader.php';
require_once IWH_PLUGIN_DIR . 'includes/attachment-hooks.php';
require_once IWH_PLUGIN_DIR . 'includes/debug-tools.php';
require_once IWH_PLUGIN_DIR . 'frontend/shortcode-world-map.php';
require_once IWH_PLUGIN_DIR . 'admin/meta-box-location.php';
require_once IWH_PLUGIN_DIR . 'admin/tools-page.php';