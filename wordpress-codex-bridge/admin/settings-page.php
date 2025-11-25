<?php
/**
 * Admin settings page for Codex Bridge plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Renders the options page for the plugin
 */
function codex_bridge_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $options = Codex_Helpers::get_options();

    // handle updated messages shown by settings API
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Codex Bridge Settings', 'codex-bridge' ); ?></h1>

        <form method="post" action="options.php">
            <?php settings_fields( 'codex_bridge_options' ); ?>
            <?php do_settings_sections( 'codex_bridge_options' ); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="codex_bridge_settings[api_url]"><?php esc_html_e( 'Codex API URL', 'codex-bridge' ); ?></label></th>
                    <td>
                        <input name="codex_bridge_settings[api_url]" type="url" id="codex_bridge_settings[api_url]" value="<?php echo esc_attr( $options['api_url'] ); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e( 'The root URL of your Codex API. Example: https://api.codex.example', 'codex-bridge' ); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="codex_bridge_settings[api_key]"><?php esc_html_e( 'Codex API Key', 'codex-bridge' ); ?></label></th>
                    <td>
                        <input name="codex_bridge_settings[api_key]" type="text" id="codex_bridge_settings[api_key]" value="<?php echo esc_attr( $options['api_key'] ); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e( 'Optional. If set, incoming webhooks must include X-Codex-Api-Key header matching this value.', 'codex-bridge' ); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e( 'Logging', 'codex-bridge' ); ?></th>
                    <td>
                        <label for="codex_bridge_settings[log_enabled]">
                            <input name="codex_bridge_settings[log_enabled]" type="checkbox" id="codex_bridge_settings[log_enabled]" value="1" <?php checked( $options['log_enabled'], '1' ); ?> />
                            <?php esc_html_e( 'Enable logging to wp-content/uploads/codex-logs/', 'codex-bridge' ); ?>
                        </label>
                    </td>
                </tr>

            </table>

            <?php submit_button(); ?>
        </form>

        <h2><?php esc_html_e( 'Recent Logs', 'codex-bridge' ); ?></h2>
        <p><?php esc_html_e( 'Below are the most recent few lines from today\'s log file (if present).', 'codex-bridge' ); ?></p>

        <div style="background:#fff;border:1px solid #ddd;padding:12px;">
            <?php
            $uploads = wp_upload_dir();
            $log_file = trailingslashit( $uploads['basedir'] ) . 'codex-logs/codex-' . date( 'Y-m-d' ) . '.log';
            if ( file_exists( $log_file ) ) :
                // Show last 2000 bytes to keep reasonable size
                $content = @file_get_contents( $log_file );
                if ( false === $content ) {
                    echo '<p>' . esc_html__( 'Unable to read log file.', 'codex-bridge' ) . '</p>';
                } else {
                    echo '<pre style="max-height:320px;overflow:auto;white-space:pre-wrap;word-break:break-word;">' . esc_html( $content ) . '</pre>';
                }
            else:
                echo '<p>' . esc_html__( 'No logs for today were found.', 'codex-bridge' ) . '</p>';
            endif;
            ?>
        </div>

    </div>
    <?php
}
