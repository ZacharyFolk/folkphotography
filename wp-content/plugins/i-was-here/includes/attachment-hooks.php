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
        }
    }
}

new IWH_Attachment_Hooks();