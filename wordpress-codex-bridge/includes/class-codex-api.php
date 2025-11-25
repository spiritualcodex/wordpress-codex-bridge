<?php
/**
 * Codex API class
 *
 * Responsible for interacting with the external Codex API using WordPress HTTP API.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Codex_API {

    protected $api_url;
    protected $api_key;

    public function __construct() {
        $opts = Codex_Helpers::get_options();
        $this->api_url = untrailingslashit( trim( $opts['api_url'] ) );
        $this->api_key = isset( $opts['api_key'] ) ? trim( $opts['api_key'] ) : '';
    }

    /**
     * Make a request to the Codex API.
     *
     * @param string $path
     * @param array $body
     * @return WP_Error|string
     */
    protected function request( $path, $body = array() ) {
        if ( empty( $this->api_url ) ) {
            return new WP_Error( 'codex_missing_url', 'Codex API URL is not configured.' );
        }

        $url = $this->api_url . '/' . ltrim( $path, '/' );

        $headers = array(
            'Content-Type' => 'application/json',
        );

        if ( ! empty( $this->api_key ) ) {
            // Send the API key as a header — users will set it in settings.
            $headers['X-Codex-Api-Key'] = $this->api_key;
        }

        $args = array(
            'headers' => $headers,
            'body'    => wp_json_encode( $body ),
            'timeout' => 20,
        );

        $resp = wp_remote_post( $url, $args );

        if ( is_wp_error( $resp ) ) {
            Codex_Helpers::log( $resp->get_error_message(), 'error' );
            return $resp;
        }

        $code = wp_remote_retrieve_response_code( $resp );
        $body = wp_remote_retrieve_body( $resp );

        if ( $code < 200 || $code >= 300 ) {
            $msg = sprintf( 'Codex API returned HTTP %d with body: %s', $code, $body );
            Codex_Helpers::log( $msg, 'error' );
            return new WP_Error( 'codex_api_error', $msg, array( 'status' => $code ) );
        }

        // Attempt to decode JSON; if not JSON, return the raw body
        $decoded = json_decode( $body, true );

        if ( null === $decoded && json_last_error() !== JSON_ERROR_NONE ) {
            return $body;
        }

        return $decoded;
    }

    /**
     * Decode a short block of text via the Codex API (used by shortcode)
     *
     * @param string $text
     * @return string|WP_Error
     */
    public function decode_soul( $text ) {
        $text = trim( wp_unslash( $text ) );

        if ( empty( $text ) ) {
            return new WP_Error( 'no_text', 'No text provided to decode.' );
        }

        $payload = array(
            'text' => $text,
        );

        $result = $this->request( '/decode', $payload );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // If the API returns structured data, try to present it sensibly
        if ( is_array( $result ) ) {
            if ( isset( $result['decoded'] ) ) {
                // Sanitize output
                return wp_kses_post( wpautop( sanitize_textarea_field( wp_strip_all_tags( $result['decoded'] ) ) ) );
            }

            // Fallback: pretty-print JSON
            return '<pre>' . esc_html( wp_json_encode( $result, JSON_PRETTY_PRINT ) ) . '</pre>';
        }

        // If it's a string return it
        return sanitize_text_field( wp_strip_all_tags( $result ) );
    }

}
