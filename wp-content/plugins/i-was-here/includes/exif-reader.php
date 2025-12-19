<?php
if (! defined('ABSPATH')) exit;

class IWH_Exif_Reader
{

    public static function read($file_path)
    {
        if (! function_exists('exif_read_data') || ! file_exists($file_path)) {
            return null;
        }

        $exif = @exif_read_data($file_path, null, true);

        iwh_log('-----=================== EXIF raw ===================-----', $exif);
        iwh_log('******************** << EXIF sections >> ********************', $exif ? array_keys($exif) : 'NO EXIF');


        return $exif;
    }
}