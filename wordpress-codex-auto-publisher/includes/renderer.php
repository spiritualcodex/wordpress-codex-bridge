<?php

if (!defined('ABSPATH')) exit;

/**
 * This file exists for future expansion.
 * Codex Cloud will send pre-rendered HTML.
 * Only minor transformations happen here.
 */

// Example: simple sanitizer for incoming HTML fragments
function codex_render_safe_html($html) {
    // Allow a conservative set of tags to avoid XSS.
    $allowed = array(
        'a'      => array('href' => true, 'title' => true, 'rel' => true),
        'p'      => array(),
        'br'     => array(),
        'strong' => array(),
        'em'     => array(),
        'ul'     => array(),
        'ol'     => array(),
        'li'     => array(),
        'img'    => array('src' => true, 'alt' => true),
        'h1'     => array(),
        'h2'     => array(),
        'h3'     => array(),
    );

    // wp_kses will handle recursive sanitization
    return wp_kses($html, $allowed);
}
