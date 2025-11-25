<?php

if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'codex_auto_pub_settings_menu');

function codex_auto_pub_settings_menu() {
    add_options_page(
        'Codex Auto Publisher',
        'Codex Auto Publisher',
        'manage_options',
        'codex-auto-pub',
        'codex_auto_pub_settings_page'
    );
}

add_action('admin_init', function() {
    register_setting('codex_auto_pub_settings', 'codex_publish_api_key');
});

function codex_auto_pub_settings_page() {
    ?>
    <div class="wrap">
        <h1>Codex Auto Publisher</h1>
        <p>This plugin auto-publishes content bundles sent from Codex Cloud.</p>

        <form method="post" action="options.php">
            <?php settings_fields('codex_auto_pub_settings'); ?>
            <?php do_settings_sections('codex_auto_pub_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">Codex API Key</th>
                    <td>
                        <input type="text"
                               name="codex_publish_api_key"
                               style="width:100%;"
                               value="<?php echo esc_attr(get_option('codex_publish_api_key')); ?>">
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
