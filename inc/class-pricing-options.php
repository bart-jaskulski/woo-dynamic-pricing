<?php
/**
 * Dentonet\WP\Dynamic_Pricing\Pricing_Options class
 *
 * @package dentonet
 */

namespace Dentonet\WP\Dynamic_Pricing;

use Dentonet\WP\Common\Component_Interface;
use WP_Post;

/**
 * Class responsible for adding options to WooCommerce.
 */
class Pricing_Options implements Component_Interface {

	/**
	 * Add all hooks and filters to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'woocommerce_variation_options_pricing', array( $this, 'action_add_dynamic_pricing_options' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'action_save_dynamic_pricing_options' ), 10, 2 );
	}

	/**
	 * Show text input for dynamic pricing threshold.
	 *
	 * @param  int     $loop Position of current variant.
	 * @param  array   $variation_data Data passed to variant.
	 * @param  WP_Post $variation WP_Post class of variant.
	 */
	public function action_add_dynamic_pricing_options( int $loop, array $variation_data, WP_Post $variation ) {
		woocommerce_wp_text_input(
			array(
				'wrapper_class' => 'form-row',
				'label' => __( 'Dynamic price ends at X of products', 'woo-dynamic-pricing' ),
				'name' => "variable_max_quantity[$loop]",
				'id' => "variable_max_quantity$loop",
				'value' => get_post_meta( $variation->ID, '_max_quantity', true ),
			)
		);
	}

	/**
	 * Handle saving meta to database.
	 *
	 * @param  int $variation_id ID of current variant.
	 * @param  int $i Position of current variant.
	 */
	public function action_save_dynamic_pricing_options( int $variation_id, int $i ) {
		update_post_meta( $variation_id, '_max_quantity', isset( $_POST['variable_max_quantity'][ $i ] ) ? absint( wp_unslash( $_POST['variable_max_quantity'][ $i ] ) ) : null ); // phpcs:ignore
	}
}
