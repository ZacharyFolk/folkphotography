<?php

/**
 * Plugin Name: i-was-here (Dev Skeleton)
 * Description: Minimal development-first plugin to log EXIF data from images on upload and via manual rescan.
 * Version: 0.2.0
 * Author: You
 */

if (! defined('ABSPATH')) exit;

// --------------------------------------------------
// CONSTANTS
// --------------------------------------------------

define('IWH_VERSION', '0.2.0');
define('IWH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IWH_LOG_ENABLED', true);

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