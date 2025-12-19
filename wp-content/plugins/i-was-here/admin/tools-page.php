<?php
add_action('admin_menu', function () {
    add_submenu_page(
        'tools.php',
        'I Was Here Settings',
        'I Was Here',
        'manage_options',
        'iwh-settings',
        'iwh_settings_page_callback'
    );
});

function iwh_settings_page_callback()
{
    if (isset($_POST['iwh_mapbox_api_key'])) {
        check_admin_referer('iwh_save_settings');
        update_option('iwh_mapbox_api_key', sanitize_text_field($_POST['iwh_mapbox_api_key']));
        echo '<div class="notice notice-success"><p>API Key saved.</p></div>';
    }

    $key = get_option('iwh_mapbox_api_key', '');
?>
<div class="wrap">
    <h1>I Was Here Settings</h1>
    <form method="post">
        <?php wp_nonce_field('iwh_save_settings'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="iwh_mapbox_api_key">Mapbox API Key</label></th>
                <td><input type="text" name="iwh_mapbox_api_key" id="iwh_mapbox_api_key"
                        value="<?php echo esc_attr($key); ?>" class="regular-text" /></td>
            </tr>
        </table>
        <?php submit_button('Save API Key'); ?>
    </form>
</div>
<?php
}