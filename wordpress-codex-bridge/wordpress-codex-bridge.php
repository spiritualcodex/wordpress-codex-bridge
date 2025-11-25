<?php
/**
 * Plugin Name: WordPress Codex Bridge
 * Plugin URI:  https://example.org/wordpress-codex-bridge
 * Description: Connects WordPress with the Codex API — provides webhook endpoint, admin settings, shortcode [codex_soul_decoder], and logging.
 * Version:     0.1.0
 * Author:      Spiritual Codex
 * Text Domain: codex-bridge
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'CODEX_BRIDGE_VERSION', '0.1.0' );
define( 'CODEX_BRIDGE_PLUGIN_FILE', __FILE__ );
define( 'CODEX_BRIDGE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CODEX_BRIDGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load required files
require_once CODEX_BRIDGE_PLUGIN_DIR . 'includes/helpers.php';
require_once CODEX_BRIDGE_PLUGIN_DIR . 'includes/class-codex-api.php';
require_once CODEX_BRIDGE_PLUGIN_DIR . 'includes/webhook-listener.php';
require_once CODEX_BRIDGE_PLUGIN_DIR . 'admin/settings-page.php';

register_activation_hook( __FILE__, 'codex_bridge_activate' );
register_deactivation_hook( __FILE__, 'codex_bridge_deactivate' );

function codex_bridge_activate() {
    // default options
    $defaults = array(
        'api_url' => '',
        'api_key' => '',
        'log_enabled' => '1',
    );

    add_option( 'codex_bridge_settings', $defaults );

    // Create logs dir in uploads
    $upload_dir = wp_upload_dir();
    $log_dir = trailingslashit( $upload_dir['basedir'] ) . 'codex-logs';

    if ( ! file_exists( $log_dir ) ) {
        wp_mkdir_p( $log_dir );
    }
}

function codex_bridge_deactivate() {
    // Leave options in place; don't delete user data on deactivation
}

add_action( 'admin_menu', 'codex_bridge_admin_menu' );
add_action( 'admin_init', 'codex_bridge_register_settings' );
add_action( 'rest_api_init', 'codex_bridge_register_rest_routes' );
add_shortcode( 'codex_soul_decoder', 'codex_bridge_shortcode_decoder' );

function codex_bridge_admin_menu() {
    add_options_page(
        __( 'Codex Bridge', 'codex-bridge' ),
        __( 'Codex Bridge', 'codex-bridge' ),
        'manage_options',
        'codex-bridge-settings',
        'codex_bridge_settings_page'
    );
}

function codex_bridge_register_settings() {
    register_setting( 'codex_bridge_options', 'codex_bridge_settings', 'codex_bridge_sanitize_options' );
}

function codex_bridge_sanitize_options( $input ) {
    $output = array();
    $output['api_url'] = isset( $input['api_url'] ) ? esc_url_raw( $input['api_url'] ) : '';
    $output['api_key'] = isset( $input['api_key'] ) ? sanitize_text_field( $input['api_key'] ) : '';
    $output['log_enabled'] = isset( $input['log_enabled'] ) && $input['log_enabled'] ? '1' : '0';
    return $output;
}

function codex_bridge_register_rest_routes() {
    register_rest_route( 'codex/v1', '/webhook', array(
        'methods'             => 'POST',
        'callback'            => array( 'Codex_Webhook_Listener', 'handle_rest_webhook' ),
        'permission_callback' => '__return_true', // validation happens in handler to allow external webhooks
    ) );
}

function codex_bridge_shortcode_decoder( $atts = array(), $content = null ) {
    // Basic shortcode rendering — shows a small form and handles form submission via POST
    $atts = shortcode_atts( array(
        'title' => 'Soul Decoder',
    ), $atts );

    ob_start();

    if ( isset( $_POST['codex_decoder_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['codex_decoder_nonce'] ) ), 'codex_decoder' ) ) {
        $text = isset( $_POST['codex_decoder_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['codex_decoder_text'] ) ) : '';
        if ( $text ) {
            $api = new Codex_API();
            $result = $api->decode_soul( $text );

            if ( is_wp_error( $result ) ) {
                echo '<div class="codex-error">' . esc_html( $result->get_error_message() ) . '</div>';
            } else {
                echo '<div class="codex-result">' . wp_kses_post( $result ) . '</div>';
            }
        }
    }

    ?>
    <div class="codex-soul-decoder">
        <h3><?php echo esc_html( $atts['title'] ); ?></h3>
        <form method="post" class="codex-decoder-form">
            <?php wp_nonce_field( 'codex_decoder', 'codex_decoder_nonce' ); ?>
            <textarea name="codex_decoder_text" rows="6" cols="50" style="width:100%;" placeholder="Describe the text to decode..."></textarea>
            <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Decode', 'codex-bridge' ); ?></button></p>
        </form>
    </div>
    <?php

    return ob_get_clean();
}

// Provide a namespaced logger function to other files
if ( ! function_exists( 'codex_bridge_log' ) ) {
    function codex_bridge_log( $message, $level = 'info' ) {
        Codex_Helpers::log( $message, $level );
    }
}

// Load textdomain for translations
add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( 'codex-bridge', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );
