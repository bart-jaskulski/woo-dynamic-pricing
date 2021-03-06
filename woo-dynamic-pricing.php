<?php
/**
 * Plugin Name: Dynamic Pricing for WooCommerce
 * Plugin URI: https://github.com/bart-jaskulski/woo-dynamic-pricing
 * Description: Opinionated dynamic pricing for WooCommerce.
 * Version: 1.1.0
 * Author: Dentonet
 * Author URI: https://dentonet.pl
 * Developer: Bart Jaskulski
 * Developer URI: https://github.com/bart-jaskulski
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 *
 * WC requires at least: 5.0
 * WC tested up to: 5.0
 *
 * @package dentonet
 */

namespace Dentonet\WP;

use Dentonet\WP\Common\Component_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class responsible for lauching all components.
 */
class Woo_Dynamic_Pricing {

	/**
	 * Array of injected components
	 *
	 * @var array
	 */
	protected $components = array();

	/**
	 * Create object loading files and inserting components.
	 */
	public function __construct() {
		// Check if WooCommerce is active.
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$this->loader();
			$this->components = $this->get_components();
		}
	}

	/**
	 * Start each component respecitively.
	 */
	public function initialize() {
		array_walk(
			$this->components,
			function ( Component_Interface $component ) {
				$component->initialize();
			}
		);
	}

	/**
	 * Load all files.
	 * TODO: Switch to autoload or sth later.
	 */
	public function loader() {
		require_once( __DIR__ . '/inc/component-interface.php' );
		require_once( __DIR__ . '/inc/class-pricing-options.php' );
		require_once( __DIR__ . '/inc/class-price-calculation.php' );
		require_once( __DIR__ . '/inc/class-apply-price.php' );
		require_once( __DIR__ . '/inc/class-update-price-form-handler.php' );
	}

	/**
	 * List and instantiate all used components here.
	 *
	 * @return array List of components
	 */
	private function get_components() : array {
		return array(
			new Dynamic_Pricing\Pricing_Options(),
			new Dynamic_Pricing\Apply_Price(),
			new Dynamic_Pricing\Update_Price_Form_Handler(),
		);
	}

}

/**
 * Start plugin when WooCommerce is up and running.
 */
add_action(
	'woocommerce_loaded',
	function () {
		( new Woo_Dynamic_Pricing() )->initialize();
	}
);
