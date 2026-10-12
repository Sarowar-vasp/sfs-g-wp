<?php
/**
 * Plugin Name: WP Post & Media Bundle
 * Description: Export and Import posts along with their featured images and metadata as a single bundle.
 * Version:     1.0.0
 * Author:      Ritupan
 * Text Domain: wp-post-media-bundle
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPMB_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPMB_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files
require_once WPMB_PATH . 'includes/class-wpmb-admin.php';
require_once WPMB_PATH . 'includes/class-wpmb-exporter.php';
require_once WPMB_PATH . 'includes/class-wpmb-importer.php';

// Initialize the plugin
function wpmb_init() {
	new WPMB_Admin();
}
add_action( 'plugins_loaded', 'wpmb_init' );
 