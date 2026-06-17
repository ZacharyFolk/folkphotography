<?php
if (! defined('ABSPATH')) exit;

class IWH_Logger
{

    private static $instance;
    private $log_file;

    private function __construct()
    {
        $upload_dir = wp_upload_dir();
        $dir = trailingslashit($upload_dir['basedir']) . 'iwh-logs';

        if (! file_exists($dir)) {
            wp_mkdir_p($dir);
        }

        $this->log_file = trailingslashit($dir) . 'dev.log';
    }

    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function log($label, $data = null)
    {
        if (! IWH_LOG_ENABLED) return;

        $entry = [
            'time'  => current_time('mysql'),
            'label' => $label,
            'data'  => $data,
        ];

        error_log(print_r($entry, true), 3, $this->log_file);
    }
}

function iwh_log($label, $data = null)
{
    IWH_Logger::get()->log($label, $data);
}