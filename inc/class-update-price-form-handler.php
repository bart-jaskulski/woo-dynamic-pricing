<?php
/**
 * Dentonet\WP\Dynamic_Pricing\Update_Price_Form_Handler class
 *
 * @package dentonet
 */

namespace Dentonet\WP\Dynamic_Pricing;

use Dentonet\WP\Dynamic_Pricing\Price_Calculation;
use Dentonet\WP\Common\Component_Interface;

/**
 * Class for dispatching and handling ajax request.
 */
class Update_Price_Form_Handler implements Component_Interface {

	/**
	 * Add all hooks and filters to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_ajax_update_cart', array( $this, 'action_ajax_update_cart_price' ) );
		add_action( 'wp_ajax_nopriv_update_cart', array( $this, 'action_ajax_update_cart_price' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'action_ajax_add_global_variables' ) );
		add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'action_update_cart_button_after_form' ), 25 );
	}

	/**
	 * Handle AJAX product price check based on quantity.
	 * Return JSON response.
	 */
	public function action_ajax_update_cart_price() {

		if (
		! isset( $_POST['dynamicPriceNonce'] )
		|| ! wp_verify_nonce( $_POST['dynamicPriceNonce'], 'update_cart' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		) {
			wp_nonce_ays( 'wrong-nonce' );
		}

		$product_id = ! empty( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
		$quantity = ! empty( $_POST['quantity'] ) ? absint( wp_unslash( $_POST['quantity'] ) ) : 0;

		$price_calculation = new Price_Calculation( $product_id, $quantity );
		$prices_array = $price_calculation->get_prices_array( true );

		wp_send_json_success( $prices_array );
	}

	/**
	 * Add button handling price update to template.
	 */
	public function action_update_cart_button_after_form() {
		?>
		<button class='is-disabled has-background' id="update_cart"><?php esc_html_e( 'Update cart', 'asysdent-theme' ); ?></button>
		<?php
	}

	/**
	 * Set global js variable with ajax url.
	 */
	public function action_ajax_add_global_variables() {
		wp_register_script( 'woo-update-price', plugin_dir_url( __DIR__ ) . 'assets/update-price.js', array(), '', true );

		if ( is_product() ) {
			$vars = array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'dynamicPriceNonce' => wp_create_nonce( 'update_cart' ),
			);
			wp_enqueue_script( 'woo-update-price' );
			wp_localize_script( 'woo-update-price', 'wooPrice', $vars );
		}
	}
}
