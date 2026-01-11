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

    /**
     * Extract specific camera settings from EXIF data
     * Returns array with ISO, Aperture, Shutter Speed, Focal Length, Camera Model, Lens
     */
    public static function extract_camera_data($exif)
    {
        if (!$exif) {
            return null;
        }

        $data = [];

        // ISO
        if (isset($exif['EXIF']['ISOSpeedRatings'])) {
            $data['iso'] = $exif['EXIF']['ISOSpeedRatings'];
        } elseif (isset($exif['EXIF']['ISO'])) {
            $data['iso'] = $exif['EXIF']['ISO'];
        }

        // Aperture (F-number)
        if (isset($exif['EXIF']['FNumber'])) {
            $fnum = $exif['EXIF']['FNumber'];
            if (strpos($fnum, '/') !== false) {
                $parts = explode('/', $fnum);
                $data['aperture'] = round($parts[0] / $parts[1], 1);
            } else {
                $data['aperture'] = $fnum;
            }
        } elseif (isset($exif['COMPUTED']['ApertureFNumber'])) {
            // Extract f-number from string like "f/2.8"
            $data['aperture'] = str_replace('f/', '', $exif['COMPUTED']['ApertureFNumber']);
        }

        // Shutter Speed
        if (isset($exif['EXIF']['ExposureTime'])) {
            $data['shutter_speed'] = $exif['EXIF']['ExposureTime'];
        }

        // Focal Length
        if (isset($exif['EXIF']['FocalLength'])) {
            $focal = $exif['EXIF']['FocalLength'];
            if (strpos($focal, '/') !== false) {
                $parts = explode('/', $focal);
                $data['focal_length'] = round($parts[0] / $parts[1]);
            } else {
                $data['focal_length'] = $focal;
            }
        }

        // Camera Make and Model
        if (isset($exif['IFD0']['Make'])) {
            $data['camera_make'] = trim($exif['IFD0']['Make']);
        }
        if (isset($exif['IFD0']['Model'])) {
            $data['camera_model'] = trim($exif['IFD0']['Model']);
        }

        // Lens Model
        if (isset($exif['EXIF']['LensModel'])) {
            $data['lens'] = trim($exif['EXIF']['LensModel']);
        } elseif (isset($exif['EXIF']['UndefinedTag:0xA434'])) {
            $data['lens'] = trim($exif['EXIF']['UndefinedTag:0xA434']);
        }

        // Date/Time Original
        if (isset($exif['EXIF']['DateTimeOriginal'])) {
            $data['date_taken'] = $exif['EXIF']['DateTimeOriginal'];
        }

        return !empty($data) ? $data : null;
    }
}