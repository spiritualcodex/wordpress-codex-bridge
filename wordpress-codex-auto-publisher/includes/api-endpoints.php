<?php

if (!defined('ABSPATH')) exit;

add_action('rest_api_init', function() {

    register_rest_route('codex/v2', '/publish', [
        'methods'  => 'POST',
        'callback' => 'codex_receive_publish_packet',
        'permission_callback' => '__return_true'
    ]);

});

function codex_receive_publish_packet($request) {

    $api_key = get_option('codex_publish_api_key');
    $headers = $request->get_headers();

    if (!isset($headers['codex-key'][0]) || $headers['codex-key'][0] !== $api_key) {
        return new WP_REST_Response([
            'status' => 'error',
            'message' => 'Invalid API key.'
        ], 403);
    }

    $payload = $request->get_json_params();

    codex_log_auto_pub('Received publish packet.');

    return codex_handle_publish($payload);
}
