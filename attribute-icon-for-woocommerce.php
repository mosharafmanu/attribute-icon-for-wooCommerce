<?php
/**
 * Plugin Name: Attribute Icon for WooCommerce
 * Plugin URI:  https://wordpress.org/plugins/attribute-icon-for-woocommerce
 * Description: Adds an icon upload field to each WooCommerce attribute — display icons alongside attribute labels on the product page.
 * Version:     1.0.0
 * Author:      mosharafmanu
 * Author URI:  https://profiles.wordpress.org/mosharafmanu
 * License:     GPL-2.0+
 * Text Domain: attribute-icon-for-woocommerce
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 */

namespace AttrIconWoo;

defined( 'ABSPATH' ) || exit;

define( __NAMESPACE__ . '\PLUGIN_FILE',    __FILE__ );
define( __NAMESPACE__ . '\PLUGIN_DIR',     plugin_dir_path( __FILE__ ) );
define( __NAMESPACE__ . '\PLUGIN_URL',     plugin_dir_url( __FILE__ ) );
define( __NAMESPACE__ . '\PLUGIN_VERSION', '1.0.0' );

/**
 * Bootstrap the plugin.
 */
function bootstrap(): void {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	require_once __DIR__ . '/src/class-attributeimagemanager.php';
	require_once __DIR__ . '/src/class-attributefrontend.php';

	$image_manager = new AttributeImageManager();
	$image_manager->init();

	$frontend = new AttributeFrontend();
	$frontend->init();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\bootstrap' );
