<?php
/**
 * Dentonet\WP\Dynamic_Pricing\Apply_Price class
 *
 * @package dentonet
 */

namespace Dentonet\WP\Dynamic_Pricing;

use Dentonet\WP\Dynamic_Pricing\Price_Calculation;
use Dentonet\WP\Common\Component_Interface;
use WC_Cart;

/**
 * Class instructing WooCommerce to diminish price based on quantity.
 */
class Apply_Price implements Component_Interface {

	/**
	 * Add all hooks and filters to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'filter_set_correct_price' ), 10, 4 );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'before_calculate_totals' ), 10, 1 );
	}

	/**
	 * Save price of single item to cart item data.
	 *
	 * @param  array $cart_item_data Array of data stored with current cart.
	 * @param  int   $product_id ID of current product.
	 * @param  int   $variation_id ID of current variation if present.
	 * @param  int   $quantity Amount of items in cart.
	 * @return array Returns cart data after filtering.
	 */
	public function filter_set_correct_price( array $cart_item_data, int $product_id, int $variation_id, int $quantity ) : array {
		if ( ! $variation_id ) {
			return $cart_item_data;
		}
		$price_calculation = new Price_Calculation( $variation_id, $quantity );
		$prices_array = $price_calculation->get_prices_array();

		$cart_item_data['one_item_price'] = $prices_array['single_price'];

		return $cart_item_data;
	}

	/**
	 * Apply diminished prices to cart items before calculating total.
	 *
	 * @param  WC_Cart $cart_obj Current cart object.
	 * @return bool True on success, false on failure.
	 */
	public function before_calculate_totals( WC_Cart $cart_obj ) : bool {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return false;
		}
		foreach ( $cart_obj->get_cart() as $key => $value ) {
			if ( isset( $value['one_item_price'] ) ) {
				$price = $value['one_item_price'];
				$value['data']->set_price( ( $price ) );
			}
		}
		return true;
	}
}
