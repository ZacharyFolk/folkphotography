<?php
if (! defined('ABSPATH')) exit;

class IWH_Attachment_Hooks
{

    public function __construct()
    {
        add_action('add_attachment', [$this, 'on_upload']);
    }

    public function on_upload($attachment_id)
    {
        $file = get_attached_file($attachment_id);
        if (! $file) return;

        iwh_log('Attachment uploaded', [
            'attachment_id' => $attachment_id,
            'file' => $file,
        ]);

        $exif = IWH_Exif_Reader::read($file);

        if ($exif) {
            update_post_meta($attachment_id, '_iwh_has_exif', 1);

            // Extract and store camera data
            $camera_data = IWH_Exif_Reader::extract_camera_data($exif);
            if ($camera_data) {
                // Store individual camera settings as post meta
                foreach ($camera_data as $key => $value) {
                    update_post_meta($attachment_id, '_iwh_' . $key, $value);
                }

                iwh_log('Camera data extracted', $camera_data);
            }

            // Extract GPS data if available
            if (isset($exif['GPS'])) {
                $this->process_gps_data($attachment_id, $exif['GPS']);
            }
        }
    }

    private function process_gps_data($attachment_id, $gps_data)
    {
        // Convert GPS coordinates to decimal format
        $lat = $this->get_gps_coordinate($gps_data, 'Latitude');
        $lng = $this->get_gps_coordinate($gps_data, 'Longitude');

        if ($lat !== null && $lng !== null) {
            update_post_meta($attachment_id, '_iwh_lat', $lat);
            update_post_meta($attachment_id, '_iwh_lng', $lng);

            iwh_log('GPS coordinates extracted', [
                'lat' => $lat,
                'lng' => $lng
            ]);
        }
    }

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

    private function gps_to_num($coordPart)
    {
        $parts = explode('/', $coordPart);
        if (count($parts) <= 0) return 0;
        if (count($parts) == 1) return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }
}

new IWH_Attachment_Hooks();