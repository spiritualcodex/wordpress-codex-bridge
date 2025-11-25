<?php
/**
 * Codex Helpers
 *
 * Lightweight helper utilities shared by the plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // exit if accessed directly
}

class Codex_Helpers {

    /**
     * Write a message to the codex logs.
     * Logs are placed under wp-content/uploads/codex-logs/ with daily files.
     *
     * @param string|array $message
     * @param string $level
     */
    public static function log( $message, $level = 'info' ) {
        $settings = get_option( 'codex_bridge_settings', array() );
        if ( isset( $settings['log_enabled'] ) && '0' === $settings['log_enabled'] ) {
            return; // logging disabled
        }

        $upload_dir = wp_upload_dir();
        $base = trailingslashit( $upload_dir['basedir'] );
        $log_dir = $base . 'codex-logs';

        if ( ! file_exists( $log_dir ) ) {
            wp_mkdir_p( $log_dir );
        }

        $file = $log_dir . '/codex-' . date( 'Y-m-d' ) . '.log';

        $time = current_time( 'mysql' );
        if ( is_array( $message ) || is_object( $message ) ) {
            $message = print_r( $message, true );
        }

        $entry = sprintf( "[%s] %s: %s\n", $time, strtoupper( $level ), $message );

        // Attempt to be safe when multiple processes write at once
        $result = @file_put_contents( $file, $entry, FILE_APPEND | LOCK_EX );

        return $result !== false;
    }

    /**
     * Utility to safely fetch plugin options
     *
     * @return array
     */
    public static function get_options() {
        $defaults = array(
            'api_url' => '',
            'api_key' => '',
            'log_enabled' => '1',
        );

        $opts = get_option( 'codex_bridge_settings', $defaults );
        return wp_parse_args( (array) $opts, $defaults );
    }

}
