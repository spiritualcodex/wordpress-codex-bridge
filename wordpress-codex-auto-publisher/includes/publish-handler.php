<?php

if (!defined('ABSPATH')) exit;

function codex_handle_publish($data) {

    $postarr = [
        'post_title'   => sanitize_text_field($data['title']),
        'post_content' => $data['content_html'],
        'post_status'  => $data['status'] ?? 'publish'
    ];

    $post_id = wp_insert_post($postarr);

    if (!$post_id) {
        codex_log_auto_pub('Failed to insert post.');
        return ['status' => 'error', 'message' => 'Post creation failed.'];
    }

    if (isset($data['meta'])) {
        update_post_meta($post_id, '_yoast_wpseo_metadesc', $data['meta']['description'] ?? '');
    }

    if (isset($data['tags'])) {
        wp_set_post_tags($post_id, $data['tags']);
    }

    if (isset($data['categories'])) {
        wp_set_post_categories($post_id, $data['categories']);
    }

    codex_log_auto_pub("Published post ID: $post_id");

    return [
        'status' => 'success',
        'post_id' => $post_id
    ];
}
