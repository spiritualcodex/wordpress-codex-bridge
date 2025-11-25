<?php
/**
 * Plugin Name: WordPress Codex Auto-Publisher
 * Description: Receives completed content bundles from Codex Cloud and auto-publishes fully formatted posts.
 * Version: 1.0.0
 * Author: Rastar13
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/api-endpoints.php';
require_once plugin_dir_path(__FILE__) . 'includes/publish-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/renderer.php';
require_once plugin_dir_path(__FILE__) . 'includes/logs.php';
require_once plugin_dir_path(__FILE__) . 'includes/helpers.php';
require_once plugin_dir_path(__FILE__) . 'admin/publisher-settings.php';
