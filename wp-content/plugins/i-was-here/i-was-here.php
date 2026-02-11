<?php

/**
 * Plugin Name: i-was-here (Dev Skeleton)
 * Description: Minimal development-first plugin to log EXIF data from images on upload and via manual rescan.
 * Version: 0.3.0
 * Author: You
 */

if (! defined('ABSPATH')) exit;

// --------------------------------------------------
// CONSTANTS
// --------------------------------------------------

define('IWH_VERSION', '0.3.0');
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