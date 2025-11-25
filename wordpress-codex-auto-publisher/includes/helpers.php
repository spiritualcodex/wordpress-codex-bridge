<?php

if (!defined('ABSPATH')) exit;

// Reserved for helper functions used by Codex Auto-Publisher in the future.

function codex_auto_pub_get_option($key, $default = '') {
    $value = get_option($key);
    if (false === $value) return $default;
    return $value;
}
