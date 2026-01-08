<?php
/**
 * Plugin Name: NeuraPHP AI Engine™
 * Description: Universal AI Infrastructure SDK for WordPress (powered by NeuraPHP)
 * Version: 1.0.0
 * Author: NeuraPHP Team
 * License: MIT
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Autoload NeuraPHP SDK (assumes SDK is installed in /neuraphp)
// Use Composer autoload or ensure NeuraPHP\Core\AI is available
if (!class_exists('NeuraPHP\\Core\\AI')) {
    require_once __DIR__ . '/../../../core/AI.php';
}

use NeuraPHP\Core\AI;

// Register a simple REST API endpoint in WordPress
add_action('rest_api_init', function () {
    register_rest_route('neuraphp/v1', '/status', [
        'methods' => 'GET',
        'callback' => function () {
            return [
                'status' => 'ok',
                'engine' => 'NeuraPHP AI Engine™',
                'version' => '1.0.0',
                'php_version' => PHP_VERSION,
                'time' => date('c')
            ];
        },
    ]);
});
