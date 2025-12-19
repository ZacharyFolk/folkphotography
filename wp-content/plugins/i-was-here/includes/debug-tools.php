<?php
if (! defined('ABSPATH')) exit;

class IWH_Debug_Tools
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_post_iwh_rescan_exif', [$this, 'rescan']);
    }

    public function menu()
    {
        add_management_page(
            'i-was-here Debug',
            'i-was-here',
            'manage_options',
            'iwh-debug',
            [$this, 'render']
        );
    }

    public function render()
    {
?>
<div class="wrap">
    <h1>i-was-here – Debug</h1>

    <form method="post" action="/localfolk/wp-admin/admin-post.php">
        <input type="hidden" name="action" value="iwh_rescan_exif">
        <?php wp_nonce_field('iwh_rescan_exif'); ?>
        <?php submit_button('Rescan EXIF for existing attachments'); ?>
    </form>


    <p>Check logs in <code>wp-content/uploads/iwh-logs/dev.log</code></p>
</div>
<?php
    }

    public function rescan()
    {

        if (! current_user_can('manage_options')) {
            wp_die('Forbidden', 403);
        }

        check_admin_referer('iwh_rescan_exif');

        $attachments = get_posts([
            'post_type'      => 'attachment',
            'posts_per_page' => 50,
        ]);

        iwh_log('Bulk EXIF rescan started', ['count' => count($attachments)]);

        foreach ($attachments as $a) {
            $file = get_attached_file($a->ID);
            if (! $file) continue;

            iwh_log('Rescanning attachment', $a->ID);
            IWH_Exif_Reader::read($file);
        }

        iwh_log('Bulk EXIF rescan complete');

        wp_redirect(admin_url('tools.php?page=iwh-debug'));
        exit;
    }
}

new IWH_Debug_Tools();