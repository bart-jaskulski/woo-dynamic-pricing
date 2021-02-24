# Useful filters and actions

* `woocommerce_quantity_input_min` templates/single-product/add-to-cart/variation-add-to-cart-button.php#L22
* `woocommerce_add_to_cart_handler` wc-form-handler.php#L785

## Look into functions

`add_to_cart_handler_variable`, but no hooks provided.

`add_to_cart_action` calls the above

`$cart->set_subtotal( $string )` maybe useful for overwriting product value
