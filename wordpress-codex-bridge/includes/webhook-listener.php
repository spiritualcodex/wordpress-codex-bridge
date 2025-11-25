<?php
/**
 * Webhook listener for Codex API
 *
 * Accepts incoming webhook requests and dispatches processing. Designed as a REST callback.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Codex_Webhook_Listener {

    /**
     * Handle incoming REST webhook requests.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function handle_rest_webhook( $request ) {
        // Log reception
        $body = $request->get_body();
        Codex_Helpers::log( "Webhook received: " . $body, 'info' );

        // Basic key validation if set
        $opts = Codex_Helpers::get_options();
        $expected_key = isset( $opts['api_key'] ) ? $opts['api_key'] : '';

        $headers = $request->get_headers();
        $incoming_key = '';
        if ( isset( $headers['x-codex-api-key'] ) ) {
            $incoming_key = is_array( $headers['x-codex-api-key'] ) ? reset( $headers['x-codex-api-key'] ) : $headers['x-codex-api-key'];
        }

        if ( ! empty( $expected_key ) && ! hash_equals( $expected_key, $incoming_key ) ) {
            Codex_Helpers::log( 'Webhook rejected — invalid API key.', 'warning' );
            return new WP_Error( 'codex_invalid_key', 'Invalid API key supplied to webhook.', array( 'status' => 401 ) );
        }

        // Attempt to decode JSON payload
        $data = json_decode( $body, true );

        if ( null === $data && json_last_error() !== JSON_ERROR_NONE ) {
            Codex_Helpers::log( 'Webhook payload not valid JSON: ' . json_last_error_msg(), 'warning' );
            return new WP_Error( 'codex_invalid_payload', 'Invalid JSON payload', array( 'status' => 400 ) );
        }

        // Basic validation: require type or id
        if ( empty( $data ) ) {
            Codex_Helpers::log( 'Webhook payload empty or missing expected structure', 'warning' );
            return new WP_Error( 'codex_empty_payload', 'Empty payload', array( 'status' => 400 ) );
        }

        // Example custom processing: fire an action so other code can hook in
        do_action( 'codex_bridge_webhook_received', $data );

        // Example: if message contained { "action":"ping" } respond with pong
        if ( ! empty( $data['action'] ) && 'ping' === $data['action'] ) {
            Codex_Helpers::log( 'Webhook ping — returning pong', 'info' );
            return rest_ensure_response( array( 'status' => 'ok', 'message' => 'pong' ) );
        }

        // Default response
        Codex_Helpers::log( 'Webhook processed successfully', 'info' );
        return rest_ensure_response( array( 'status' => 'ok', 'received' => true ) );
    }

}
