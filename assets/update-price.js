const button = document.getElementById( 'update_cart' )
const form = document.getElementById( 'add_to_cart_form' );

button.onclick = () => {
	const type = document.getElementById( 'typ-biletu' );
	const quantity = document.getElementsByClassName( 'quantity' )[ 0 ].children
		.quantity;
	const priceData = getCurrentVariantData( type.value );
	const data = {
		method: "POST",
		credentials: 'same-origin',
		body: new URLSearchParams( {
			action: 'update_cart',
			type: type.value,
			quantity: quantity.value,
			product_id: priceData.variation_id,
			price: priceData.display_price,
			dynamicPriceNonce: wooPrice.dynamicPriceNonce
		} ),
	}

	// Add class for CSS styling.
	form.classList.add( 'processing' );

	fetch( woocommerce_params.ajax_url, data )
		.then( response => response.json() )
		.then( res => {
			if ( res.success ) {
				const discountAmount = document.getElementById( 'discount_amount' );
				const singleTicketAmount = document.getElementById(
					'single_ticket_amount' );

				let discount = res.data.discount;
				let single = res.data.single_price;

				discountAmount.innerText = `${discount}%`;
				singleTicketAmount.innerHTML = single;

				// Remove class when finished.
				form.classList.remove( 'processing' )
			}
		} );
}

/**
 * Get all variant data from form attribute.
 * @param  {string} variantName Name of the variant you need.
 * @return {object} Returns object with associated variant data.
 */
function getCurrentVariantData( variantName ) {
	const formData = JSON.parse( form.dataset.product_variations );
	let returnValue = false;
	formData.forEach( item => {
		if ( item[ 'attributes' ][ 'attribute_typ-biletu' ] === variantName ) {
			returnValue = item;
		}
	} )

	return returnValue;
}
