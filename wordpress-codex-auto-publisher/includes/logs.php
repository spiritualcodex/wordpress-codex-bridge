<?php

if (!defined('ABSPATH')) exit;

function codex_log_auto_pub($message) {

    $upload_dir = wp_upload_dir();
    $dir = $upload_dir['basedir'] . '/codex-auto-publisher';

    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }

    $file = $dir . '/log.txt';
    $line = date('Y-m-d H:i:s') . ' - ' . $message . "\n";

    file_put_contents($file, $line, FILE_APPEND);
}
