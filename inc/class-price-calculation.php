<?php
/**
 * Dentonet\WP\Dynamic_Pricing\Price_Calculation class
 *
 * @package dentonet
 */

namespace Dentonet\WP\Dynamic_Pricing;

/**
 * Class which holds calculation of diminishing price.
 */
class Price_Calculation {

	/**
	 * Base product price
	 *
	 * @var string
	 */
	private string $initial_price;

	/**
	 * Price of single product after calculation.
	 *
	 * @var string
	 */
	private string $single_price;

	/**
	 * Total price of products after calculation.
	 *
	 * @var string
	 */
	private string $total_price;

	/**
	 * Percent of discount
	 *
	 * @var int
	 */
	private int $discount;

	/**
	 * Create an instance of calculation handler.
	 *
	 * @param  int $product_id ID of product to calculate price.
	 * @param  int $quantity Amount of items.
	 */
	public function __construct(
	private int $product_id,
	private int $quantity
	) {}

	/**
	 * Get prices after calculation.
	 *
	 * @param bool $formatted Show prices in currency format.
	 * @return array Array of prices on success.
	 */
	public function get_prices_array( bool $formatted = false ) : array {
		$prices_array = array();
		if ( $this->calculate_prices() ) {

			$prices_array = array(
				'initial_price' => "{$this->initial_price} zł",
				'single_price' => "{$this->single_price} zł",
				'total_price' => "{$this->total_price} zł",
				'discount' => $this->discount,
			);

		}

		return $prices_array;
	}

	/**
	 * Calculate prices from equation and save them to object
	 *
	 * @return bool True on success, false if failed.
	 */
	private function calculate_prices() : bool {
		$product = wc_get_product( $this->product_id );
		$quantity = $this->quantity;
		if ( $product ) {
			$initial_price = $product->get_price();

			if ( $initial_price < 1 ) {
				return false;
			}
			// Do not exceed that quantity when diminishing price.
			$threshold = $product->get_meta( '_max_quantity' );
			// Return early if product not marked for dynamic pricing.
			if ( ! isset( $threshold ) ) {
				return false;
			}
			$quantity_for_single = ( $quantity > $threshold ) ? $threshold : $quantity;
			// Find out price for single item when discount applied.
			$single_price = $initial_price - ( sqrt( $quantity_for_single - 1 ) * 10 );
			$total_price = $quantity * $single_price;
			// Set discount as positive percentage float numeric value.
			$discount = abs( ( ( $single_price / $initial_price ) - 1 ) * 100 );

			$this->initial_price = number_format( $initial_price, wc_get_price_decimals() );
			$this->single_price = number_format( $single_price, wc_get_price_decimals() );
			$this->total_price = number_format( $total_price, wc_get_price_decimals() );
			$this->discount = round( $discount, 2 );

			return true;
		}

		return false;
	}
}
